<?php

namespace App\Http\Controllers;

use App\Models\Cartela;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\GamePlayer;
use App\Services\JoinGameService;
use App\Services\StartGameService;
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

    public function selectCartela($categoryId, $cartelaName = null): Response
    {
        return Inertia::render('Game/Initiate/Cartela',[
            'gameCategory' => GameCategory::findOrFail($categoryId),
            'cartela' => Inertia::lazy(function () use ($cartelaName) {
                if (!$cartelaName)
                    return null;

                return Cartela::where('name', $cartelaName)->first();
            })
        ]);
    }

    public function playGame(Request $request): Response
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);

        $game = Game::find($request->game_id)->load(['gameCategory', 'players']);

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

    public function joinGame(Request $request): Response
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
