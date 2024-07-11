<?php

namespace App\Http\Controllers;

use App\Events\StartGameEvent;
use App\Models\Cartela;
use App\Models\Cashier;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GamePlayer;
use App\Services\DrawGameService;
use App\Services\SettleGameService;
use App\Services\StartGameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function index()
    {
        return Inertia::render('Game/Cashier/Initiate', [
            'gameCategories' => GameCategory::all()
        ]);
    }

    public function finance()
    {
        return Inertia::render('Cashier/Finance');
    }

    public function createGame(Request $request)
    {
        $gameCategory = GameCategory::find($request->gameCategoryId);

        // Check if there is a pending game where the category is the same and is a tv game
        $game = Game::where('game_category_id', $gameCategory->id)
            ->where('is_tv_game', true)
            ->whereIn('status', [Game::STATUS_PENDING, Game::STATUS_ACTIVE])
            ->first();

        if (!$game) {
            $game = Game::create([
                'game_category_id' => $gameCategory->id,
                'status' => Game::STATUS_PENDING,
                'is_tv_game' => true,
            ]);
        }


        return Inertia::render('Game/Cashier/Add', [
            'gameCategory' => $gameCategory,
            'game' => $game,
            'gamePlayersCount' => $game->players()->count()
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
            'gamePlayersCount' => $game->players()->count()
        ]);

        $cashier = Cashier::where('user_id', auth()->user()->id)->first();
        $cashier->update(['balance' => $cashier->balance + $game->gameCategory->amount]);

        return redirect()->back()->with('success', 'Player added successfully');
    }

    public function startGame(Request $request, $cartelaName = null, $gameId = null)
    {
        $request->validate([
            'game_id' => 'nullable|exists:games,id',
        ]);

        $game = Game::whereIn('status', [Game::STATUS_PENDING, Game::STATUS_ACTIVE])
            ->where('is_tv_game', true)
            ->first();


        $game->load(['players', 'gameCategory']);

        if ($game->status === Game::STATUS_PENDING) {

            $totalPlayers = $game->players()->count();

            // Todo: Change the percentage value to .env variable
            $game->update(['winner_net_amount' => $totalPlayers * (int)$game->gameCategory->amount * 0.9]);
            $game->update(['status' => Game::STATUS_ACTIVE]);

            if (!$game->draw_numbers) {
                DrawGameService::drawGame($game->id);
            }
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

        return redirect()->route('cashier.game.initiate')->with('success', 'Game completed successfully');
    }

}
