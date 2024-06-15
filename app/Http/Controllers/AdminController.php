<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class AdminController extends Controller
{
    public function index(): Response
    {
        $totalGames = Game::count();

        $totalPlayers = User::where('type', 'player')->count();

        // Fetch pending games data
        $pendingGames = Game::where('status', 'pending')->get();

        // Fetch players on active game status
        $activePlayers = Game::where('status', 'active')
            ->with('players.player.user')
            ->get()
            ->pluck('players')
            ->flatten()
            ->pluck('player')
            ->flatten()
            ->pluck('user')
            ->unique()
            ->count();

        // Fetch recent winners
        $recentWinners = Game::where('status', 'completed')
            ->whereNotNull('winner_player_id')
            ->with(['players.player.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($game) {
                $winnerPlayer = $game->players->firstWhere('player_id', $game->winner_player_id);
                return [
                    'game' => $game,
                    'winner' => $winnerPlayer ? $winnerPlayer->player->user : null,
                ];
            });

        return Inertia::render('Admin/Dashboard/Index', [
            'authUser' => auth()->user(),
            'totalPlayers' => $totalPlayers,
            'pendingGames' => $pendingGames,
            'recentWinners' => $recentWinners,
            'totalGames' => $totalGames,
            'activePlayers' => $activePlayers,
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

    public function player($userId): Response
    {
        $player = User::find($userId);

        $recentActivities = Activity::where('causer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $gamesPlayed = Game::whereHas('players.player', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        return Inertia::render('Admin/Users/Player', [
            'player' => $player,
            'recentActivities' => $recentActivities,
            'gamesPlayed' => $gamesPlayed,
        ]);
    }
}
