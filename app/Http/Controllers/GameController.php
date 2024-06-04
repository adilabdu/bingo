<?php

namespace App\Http\Controllers;

use App\Models\Cartela;
use App\Models\GameCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                $l = Cartela::where('name', $cartelaName)->first();
                Log::info($l);
                return $l;
            })
        ]);
    }

    public function playGame()
    {
        return Inertia::render('Game/Play');
    }
}
