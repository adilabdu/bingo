<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class AdminController extends Controller
{
    public function index(Request $request): Response
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

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

        // Calculate total revenue and profit with date filters if provided
        $revenueQuery = Game::join('game_categories', 'games.game_category_id', '=', 'game_categories.id');

        if ($startDate && $endDate) {
            $revenueQuery->whereBetween('games.scheduled_at', [$startDate, $endDate]);
        }

        $totalRevenue = $revenueQuery->sum('game_categories.amount');
        $totalProfit = $totalRevenue * 0.10;

        return Inertia::render('Admin/Dashboard/Index', [
            'authUser' => auth()->user(),
            'totalPlayers' => $totalPlayers,
            'pendingGames' => $pendingGames,
            'recentWinners' => $recentWinners,
            'totalGames' => $totalGames,
            'activePlayers' => $activePlayers,
            'totalRevenue' => $totalRevenue,
            'totalProfit' => $totalProfit,
            'startDate' => $startDate,
            'endDate' => $endDate,
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

        return Inertia::render('Admin/Games/Single', [
            'game' => $game,
        ]);
    }

    public function player($userId): Response
    {
        $player = User::with('player')->find($userId);

        if (!$player) {
            // Handle the case where the player is not found
            abort(404, 'Player not found');
        }

        $recentActivities = Activity::where('causer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $gamesPlayed = Game::whereHas('players.player', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        // Calculate the total won amount for the specific player
        $totalWonAmount = Game::where('winner_player_id', $player->id)
            ->sum('winner_net_amount');

        // Calculate the total wagered amount by joining game_categories
        $totalWageredAmount = Game::whereHas('players.player', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->join('game_categories', 'games.game_category_id', '=', 'game_categories.id')
            ->sum('game_categories.amount');

        // Calculate the total loss amount
        $totalLossAmount = $totalWageredAmount - $totalWonAmount;

        return Inertia::render('Admin/Users/Player', [
            'player' => $player,
            'recentActivities' => $recentActivities,
            'gamesPlayed' => $gamesPlayed,
            'totalWonAmount' => $totalWonAmount,
            'totalWageredAmount' => $totalWageredAmount,
            'totalLossAmount' => $totalLossAmount,
        ]);
    }

    public function register(): Response
    {
        return Inertia::render('Admin/Users/Register');
    }

    public function profile(): Response
    {
        return Inertia::render('Admin/Profile/Edit');
    }
}
