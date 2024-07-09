<?php

namespace App\Http\Controllers;

use App\Models\Cartela;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GamePlayer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function index()
    {
        return Inertia::render('Game/Cashier/Initiate',[
            'gameCategories' => GameCategory::all()
        ]);
    }

    public function createGame(Request $request)
    {
        $gameCategory = GameCategory::find($request->gameCategoryId);

        // Check if there is a pending game where the category is the same and is a tv game
        $game = Game::where('game_category_id', $gameCategory->id)
            ->where('is_tv_game', true)
            ->where('status', Game::STATUS_PENDING)
            ->first();

        if (!$game) {
            $game = Game::create([
                'game_category_id' => $gameCategory->id,
                'status' => Game::STATUS_PENDING,
                'is_tv_game' => true,
            ]);
        }


        return Inertia::render('Game/Cashier/Add',[
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
            return redirect()->back()->with('error', 'Cartela already in use');
        }
        GamePlayer::create([
            'game_id' => $game->id,
            'cartela_id' => $cartela->id,
            'gamePlayersCount' => $game->players()->count()
        ]);

        return redirect()->back()->with('success', 'Player added successfully');
    }
}
