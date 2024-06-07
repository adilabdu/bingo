<?php

namespace App\Http\Controllers;

use App\Models\Cartela;
use App\Models\GameCategory;
use App\Services\GameService;
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

    public function playGame()
    {
        return Inertia::render('Game/Play');
    }

    public function joinGame(Request $request)
    {
        $request->validate([
            'game_category_id' => 'required|exists:game_categories,id',
            'cartela_id' => 'required|exists:cartelas,id',
        ]);

        $game = GameService::startGame($request->cartela_id, $request->game_category_id);

        return Inertia::render('Game/Initiate/Join',[
            'gameCategory' => GameCategory::findOrFail($request->game_category_id),
            'cartela' => Cartela::findOrFail($request->cartela_id),
            'game' => $game
        ]);
    }
}
