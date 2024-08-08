<?php

namespace App\Http\Controllers;

use App\Events\AddCashierPlayerEvent;
use App\Models\Cartela;
use App\Models\Cashier;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GamePlayer;
use App\Services\DrawGameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CashierGameController extends Controller
{
    public function index()
    {
        return Inertia::render('Game/Cashier/Initiate', [
            'gameCategories' => GameCategory::all()
        ]);
    }

    public function createGame(Request $request)
    {
        $gameCategory = GameCategory::find($request->gameCategoryId);

        $gameToRepeat = null;
        if ($request->repeatGame) {
            $gameToRepeat = Game::find($request->gameToRepeatId);
            $gameToRepeat->update(['status' => Game::STATUS_COMPLETED]);
        }

        $cashier = auth()->user()->cashier->load('branch.agent');
        if (!$this->checkIfCashierIsAllowedToPlay($cashier)) {
            return redirect()->back()->with('error', 'You are not allowed to play. Contact your agent.');
        }
        // Check if there is a pending game where the category is the same and is a tv game
        $game = Game::where('game_category_id', $gameCategory->id)
            ->where('is_tv_game', true)
            ->where('cashier_id', $cashier->id)
            ->whereIn('status', [Game::STATUS_PENDING, Game::STATUS_ACTIVE])
            ->first();

        if (!$game) {
            $game = Game::create([
                'game_category_id' => $gameCategory->id,
                'status' => Game::STATUS_PENDING,
                'is_tv_game' => true,
                'cashier_id' => auth()->user()->cashier->id,
            ]);
        }

        $percentage = auth()->user()->cashier->load('branch')->branch->percent;
        $selectedCartelas = $game->cartelas()->get();

        AddCashierPlayerEvent::dispatch($game, $selectedCartelas);

        if ($request->repeatGame) {
            if ($gameToRepeat) {
                $cashier = Cashier::where('user_id', auth()->user()->id)->first()->load('branch.agent');
                $gameToRepeat->cartelas()->get()->each(function ($cartela) use ($game, $cashier) {
                    $cartelaInUse = GamePlayer::where('cartela_id', $cartela->id)
                        ->whereRelation('game', function ($query) use ($game) {
                            $query->where('id', $game->id);
                        })->exists();
                    if ($cartelaInUse) {
                        return;
                    }
                    GamePlayer::create([
                        'game_id' => $game->id,
                        'cartela_id' => $cartela->id,
                    ]);
                    $cashier->update(['balance' => $cashier->balance + $game->gameCategory->amount]);
                });

                $cashier->branch->agent->update(['balance' => $cashier->branch->agent->balance - ($game->profit * $cashier->branch->agent->profit_percentage / 100)]);
                $selectedCartelas = $game->cartelas()->get();
                AddCashierPlayerEvent::dispatch($game, $selectedCartelas);
            }
        }

        return Inertia::render('Game/Cashier/Add', [
            'gameCategory' => $gameCategory,
            'game' => $game,
            'selectedCartelas' => $selectedCartelas,
            'percentage' => $percentage
        ]);
    }

    public function addPlayers(Request $request)
    {
        $request->validate([
            'gameId' => 'required|exists:games,id',
            'cartelaName' => 'required|string',
        ]);

        $game = Game::find($request->gameId);
        $cartela = Cartela::where('name', $request->cartelaName)->first();

        // Check if the cartela is already in use
        $cartelaInUse = GamePlayer::where('cartela_id', $cartela->id)
            ->whereRelation('game', function ($query) use ($game) {
                $query->where('id', $game->id);
            })->exists();

        if ($cartelaInUse) {
            return redirect()->back()->withErrors(['cartelaName' => 'Cartela already in use']);
        }

        GamePlayer::create([
            'game_id' => $game->id,
            'cartela_id' => $cartela->id,
            'gamePlayersCount' => $game->players()->count(),
        ]);

        $cashier = Cashier::where('user_id', auth()->user()->id)->first();
        $cashier->update(['balance' => $cashier->balance + $game->gameCategory->amount]);

        return redirect()->back()->with('success', 'Player added successfully');
    }

    public function startGame(Request $request, $cartelaName = null, $gameId = null, $gameCategoryId = null)
    {
        $request->validate([
            'game_id' => 'nullable|exists:games,id',
            'game_category_id' => 'nullable|exists:game_categories,id',
        ]);

        $cashier = auth()->user()->cashier;

        $game = Game::whereIn('status', [Game::STATUS_PENDING, Game::STATUS_ACTIVE])
            ->where('is_tv_game', true)
            ->where('cashier_id', $cashier->id)
            ->where('game_category_id', $request->input('game_category_id', $gameCategoryId))
            ->first();


        $game->load(['players', 'gameCategory']);

        if ($game->status === Game::STATUS_PENDING) {

            $totalPlayers = $game->players()->count();

            $percent = (float)$cashier->branch->percent;
            $totalAmount = $totalPlayers * (int)$game->gameCategory->amount;
            $winnerNetAmount = $totalAmount * ((100 - $percent) / 100);

            $game->update(['winner_net_amount' => $winnerNetAmount]);

            $profit = $totalAmount - $winnerNetAmount;
            $game->update(['profit' => $profit]);

            $game->update(['status' => Game::STATUS_ACTIVE]);

            if (!$game->draw_numbers) {
                DrawGameService::drawGame($game->id);
            }

            $selectedCartelas = $game->cartelas()->get();
            AddCashierPlayerEvent::dispatch($game, $selectedCartelas, true);

            activity()
                ->causedBy(auth()->user()->cashier)
                ->event('Start Game ' . $game->id)
                ->performedOn($game)
                ->withProperties(['amount' => $totalAmount . ' Br'])
                ->log('Started a game with amount: ' . $totalAmount . ' Br');
        }

        $batchIndex = $request->input('batch_index', 0);

        return Inertia::render('Game/Cashier/Play', [
            'game' => $game,
            'gamePlayersCount' => $game->players()->count(),
            'nextBatchIndex' => $batchIndex + 1,
            'totalPlayers' => $game->players->count(),
            'drawnNumbers' => $game->draw_numbers,
            'cartela' => Inertia::lazy(function () use ($cartelaName, $gameId) {
                if (!$cartelaName)
                    return null;

                // Return cartela if the cartela is in the game
                $cartela = Cartela::where('name', $cartelaName)->first();
                // Check if is in the game
                $cartelaInGame = GamePlayer::where('cartela_id', $cartela->id)
                    ->where('game_id', $gameId)
                    ->first();

                if (!$cartelaInGame)
                    return null;

                return $cartela;
            })
        ]);
    }

    public function finish(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);

        $game = Game::find($request->game_id);

        if (!$game->is_tv_game || $game->status !== Game::STATUS_ACTIVE) {
            return redirect()->route('cashier.game.initiate')->with('error', 'Invalid game');
        }

        $game->update(['status' => Game::STATUS_COMPLETED]);

        $cashier = Cashier::where('user_id', auth()->user()->id)->first()->load('branch.agent');
        $cashier->update(['balance' => $cashier->balance - $game->winner_net_amount]);
        $cashier->branch->agent->update(['balance' => $cashier->branch->agent->balance - ($game->profit * $cashier->branch->agent->profit_percentage / 100)]);

        activity()
            ->causedBy(auth()->user()->cashier)
            ->performedOn($game)
            ->event('Finish Game ' . $game->id)
            ->withProperties(['amount' => $game->winner_net_amount . ' Br'])
            ->log('Game completed successfully, with payout amount: ' . $game->winner_net_amount . ' Br');

        return redirect()->route('cashier.game.initiate')->with('success', 'Game completed successfully');
    }

    public function remove($cartelaName, $gameId)
    {
        $cartela = Cartela::where('name', $cartelaName)->first();
        $game = Game::findOrFail($gameId);

        if (!$game || !$cartela) {
            return redirect()->back()->with('error', 'Game not found');
        }

        $gamePlayer = GamePlayer::where('game_id', $game->id)
            ->where('cartela_id', $cartela->id)
            ->where('player_id', null)
            ->first();

        if (!$gamePlayer) {
            return redirect()->back()->with('error', 'Player not found');
        }

        $gamePlayer->delete();

        $cashier = Cashier::where('user_id', auth()->user()->id)->first();
        $cashier->update(['balance' => $cashier->balance - $game->gameCategory->amount]);

        return redirect()->to('/cashier/game/create/'.$game->game_category_id)->with('success', 'Player removed successfully');
    }

    public function cartela($name = null)
    {
        $cartela = Cartela::where('name', $name)->first();
        return Inertia::render('Game/Cashier/Cartela',[
            'cartela' => $cartela
        ]);
    }

    public function playCartela($cartelaId)
    {
        $cartela = Cartela::find($cartelaId);
        return Inertia::render('Game/Cashier/PlayCartela',[
            'cartela' => $cartela
        ]);
    }

    private function checkIfCashierIsAllowedToPlay( $cashier)
    {
      if ($cashier->branch->agent->balance < 10 || !$cashier->branch->agent->is_active) {
          return false;
      }

      return true;
    }
}
