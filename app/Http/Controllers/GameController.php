<?php

namespace App\Http\Controllers;

use App\Models\GameCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameController extends Controller
{
    public function index()
    {
        return Inertia::render('Game/Initiate/Index', [
            'categories' => GameCategory::all()
        ]);
    }

    public function selectCartela()
    {
        return Inertia::render('Game/Initiate/Cartela');
    }

    public function playGame()
    {
        return Inertia::render('Game/Play');
    }
}
