<?php

namespace App\Http\Controllers;

use App\Events\GameResultEvent;
use App\Http\Requests\CallBingoRequest;
use App\Models\Cartela;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GamePlayer;
use App\Services\JoinGameService;
use App\Services\SettleGameService;
use App\Services\StartGameService;
use App\Services\BingoCallValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function index(): Response | RedirectResponse
    {
        $gameCategories = GameCategory::with(['games' => function ($query) {
            $query->where('status', Game::STATUS_PENDING);
        }])->get();

        $gameCategories->each(function ($gameCategory) {
            $gameCategory->games->each(function ($game) {
                $game->players_count = $game->players()->count();
            });
        });

        $pendingGameRedirect = $this->checkPendingGame();
        if ($pendingGameRedirect) {
            return $pendingGameRedirect;
        }

        $activeGameRedirect = $this->checkActiveGame();
        if ($activeGameRedirect) {
            return $activeGameRedirect;
        }

        return Inertia::render('Game/Initiate/Index', [
            'gameCategories' => $gameCategories
        ]);
    }

    public function selectCartela(Request $request, $categoryId, $cartelaName = null): Response | RedirectResponse
    {
        $page = $request->input('page', 'Game/Initiate/Cartela');

        $pendingGameRedirect = $this->checkPendingGame();
        if ($pendingGameRedirect) {
            return $pendingGameRedirect;
        }

        $activeGameRedirect = $this->checkActiveGame();
        if ($activeGameRedirect) {
            return $activeGameRedirect;
        }

        return Inertia::render($page,[
            'gameCategory' => GameCategory::findOrFail($categoryId),
            'cartela' => Inertia::lazy(function () use ($cartelaName, $categoryId) {
                if (!$cartelaName)
                    return ['error' => 'Cartela name is required.'];

                $cartela =  Cartela::where('name', $cartelaName)->first();
                // Check if the cartela is already in use
                $cartelaInUse = GamePlayer::where('cartela_id', $cartela->id)
                    ->whereHas('game', function ($query) use ($categoryId) {
                        $query->where('game_category_id', $categoryId)
                        ->whereIn('status', [Game::STATUS_ACTIVE, Game::STATUS_PENDING]);
                    })
                    ->first();
                if ($cartelaInUse)
                    return ['error' => 'Cartela is already in use'];
                return $cartela;
            })
        ]);
    }

    public function playGame(Request $request): RedirectResponse | Response
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);

        $game = Game::find($request->game_id)->load(['gameCategory', 'players']);

        if (!$game) {
            return redirect()->route('game.initiate');
        }

        $this->checkPendingGame();

        // Validate player participation in the game
        $playerGame = GamePlayer::where('game_id', $game->id)
            ->where('player_id', auth()->user()->player->id)
            ->firstOrFail();

        $playerCartela = Cartela::find($playerGame->cartela_id);

        // Check if a batch request is made
        $batchIndex = $request->input('batch_index', 0);
        $drawnNumbers = StartGameService::getDrawnNumbersForBatch($game, $batchIndex);

        return Inertia::render('Game/Play', [
            'game' => $game,
            'playerCartela' => $playerCartela,
            'totalPlayers' => $game->players->count(),
            'drawnNumbers' => $drawnNumbers,
            'nextBatchIndex' => $batchIndex + 1,
        ]);
    }

    public function joinGame(Request $request): Response | RedirectResponse
    {
        $request->validate([
            'game_category_id' => 'required|exists:game_categories,id',
            'cartela_id' => 'required|exists:cartelas,id',
        ]);

        $player = auth()->user()->load('player')->player;

        $gameCategory = GameCategory::findOrFail($request->game_category_id);

        $activeGameRedirect = $this->checkActiveGame();
        if ($activeGameRedirect) {
            return $activeGameRedirect;
        }

        if ($player->balance < $gameCategory->amount) {
            return redirect()->route('game.initiate');
        }

        // Check if the cartela is already in use
        $cartelaInUse = GamePlayer::where('cartela_id', $request->cartela_id)
            ->whereHas('game', function ($query) {
                $query->whereIn('status', [Game::STATUS_ACTIVE, Game::STATUS_PENDING]);
            })
            ->first();

        Log::info($cartelaInUse);
        if ($cartelaInUse) {
            Log::info("Cartela in use ". $cartelaInUse->game_id);
             // Todo: Handle Error
            return redirect()->back()->with('error' ,'Cartela is already in use');
        }

        $game = JoinGameService::startGame($request->cartela_id, $request->game_category_id);

        $remainingSeconds = floor(now()->diffInSeconds($game->scheduled_at, true));

        $totalPlayers = $game->players()->count();

        return Inertia::render('Game/Initiate/Join',[
            'gameCategory' => GameCategory::findOrFail($request->game_category_id),
            'cartela' => Cartela::findOrFail($request->cartela_id),
            'game' => $game,
            'remainingSeconds' => $remainingSeconds,
            'totalPlayers' => $totalPlayers,
        ]);
    }

    public function callBingo(CallBingoRequest $request, Cartela $cartela, Game $game): void
    {
        $isBingoCallValid = BingoCallValidationService::validate(
            $game, $cartela,
            $request->input('selected_numbers'),
            $request->integer('draw_numbers_cut_off_index')
        );


        if (!$isBingoCallValid) {
            // Todo: Handle Error
//            return redirect()->route('game.initiate');
        }

        $game->update([
            'status' => Game::STATUS_COMPLETED,
            'winning_numbers' => $request->input('selected_numbers'),
            'winner_player_id' => auth()->user()->player->id,
        ]);

        event(new GameResultEvent($game, auth()->user(), $request->input('selected_numbers'), $cartela));

        SettleGameService::settleWinnerBalance($game);
    }

    private function checkActiveGame(): ?RedirectResponse
    {
        $player = auth()->user()->load('player');

        $activeGame = GamePlayer::where('player_id', $player->player->id)
            ->whereHas('game', function ($query) {
                $query->whereIn('status', [Game::STATUS_ACTIVE]);
            })->first();

        if ($activeGame) {
            Log::info($activeGame->game_id);
            return redirect()->route('game.play', [
                'game_id' => $activeGame->game_id,
            ]);
        }

        return null;
    }

    private function checkPendingGame(): ?RedirectResponse
    {
        $player = auth()->user()->load('player');

        $pendingGame = GamePlayer::where('player_id', $player->id)
            ->whereHas('game', function ($query) {
                $query->whereIn('status', [Game::STATUS_PENDING]);
            })->first();


        if ($pendingGame) {
            return redirect()->route('game.join', [
                'game_category_id' => $pendingGame->game->game_category_id,
                'cartela_id' => $pendingGame->cartela_id
            ]);
        }

        return null;
    }
}


