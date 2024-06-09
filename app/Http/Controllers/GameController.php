<?php

namespace App\Http\Controllers;

use App\Models\Cartela;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GamePlayer;
use App\Services\JoinGameService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function index()
    {
        return Inertia::render('Game/Initiate/Index', [
            'gameCategories' => GameCategory::all()
        ]);
    }

    public function selectCartela($categoryId, $cartelaName): Response
    {
        return Inertia::render('Game/Initiate/Cartela',[
            'gameCategory' => GameCategory::findOrFail($categoryId),
            'cartela' => Inertia::lazy(function () use ($cartelaName) {
                return Cartela::where('name', $cartelaName)->first();
            })
        ]);
    }

    public function playGame(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);

        // Check if game is active
        $game = Game::find($request->game_id)->load('gameCategory');

        if ($game->status !== Game::STATUS_ACTIVE) {
            return redirect()->route('game.initiate');
        }

        $playerGame = GamePlayer::where('game_id', $game->id)
            ->where('player_id', auth()->user()->load('player')->player->id)
            ->first();

        $playerCartela = Cartela::find($playerGame->cartela_id);

        return Inertia::render('Game/Play', [
            'game' => $game,
            'playerCartela' => $playerCartela,
            'totalPlayers' => $game->players->count(),
        ]);
    }

    public function joinGame(Request $request)
    {
        $request->validate([
            'game_category_id' => 'required|exists:game_categories,id',
            'cartela_id' => 'required|exists:cartelas,id',
        ]);

        $game = JoinGameService::startGame($request->cartela_id, $request->game_category_id);

        return Inertia::render('Game/Initiate/Join',[
            'gameCategory' => GameCategory::findOrFail($request->game_category_id),
            'cartela' => Cartela::findOrFail($request->cartela_id),
            'game' => $game
        ]);
    }
}
