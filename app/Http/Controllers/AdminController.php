<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        $totalPlayers = User::where('type', 'player')->count();

        return Inertia::render('Admin/Dashboard/Index', [
            'authUser' => auth()->user(),
            'totalPlayers' => $totalPlayers,
        ]);
    }

    public function users(Request $request): Response
    {
        $query = $request->input('search');

        $usersQuery = User::query();

        if ($query) {
            $usersQuery->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', '%'.$query.'%')
                    ->orWhere('email', 'like', '%'.$query.'%');
            });
        }

        $users = $usersQuery->paginate(10);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function games(Request $request): Response
    {
        // Fetch all games count
        $totalGames = Game::count();

        // Fetch all games today count
        $totalGamesToday = Game::whereDate('created_at', today())->count();

        // Fetch active games count
        $activeGames = Game::where('status', 'active')->count();

        $query = Game::with('gameCategory', 'players');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $games = $query->paginate(10);

        return Inertia::render('Admin/Games/Index', [
            'games' => $games,
            'filters' => $request->only('status'),
            'totalGames' => $totalGames,
            'totalGamesToday' => $totalGamesToday,
            'activeGames' => $activeGames,
        ]);
    }

    public function game(Game $id): Response
    {
        $game = Game::with('gameCategory', 'players.player.user', 'players.cartela')->find($id->id);

        $winners = $game->players->where('is_winner', true);

        return Inertia::render('Admin/Games/Single', [
            'game' => $game,
            'winners' => $winners,
        ]);
    }
}
