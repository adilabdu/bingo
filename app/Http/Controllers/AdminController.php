<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Branch;
use App\Models\Cartela;
use App\Models\Game;
use App\Models\Player;
use App\Models\PWC;
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
        $todayRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfDay())
            ->sum('profit');

        $thisMonthRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('profit');

        $thisWeekRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfWeek())
            ->sum('profit');

        $totalRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->sum('profit');

        $todayCashierRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfDay())
            ->whereNotNull('cashier_id')
            ->sum('profit');

        $thisMonthCashierRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfMonth())
            ->whereNotNull('cashier_id')
            ->sum('profit');

        $thisWeekCashierRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfWeek())
            ->whereNotNull('cashier_id')
            ->sum('profit');

        $totalCashierRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->whereNotNull('cashier_id')
            ->sum('profit');

        $totalPlayerRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->whereNull('cashier_id')
            ->sum('profit');

        $todayPlayerRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfDay())
            ->whereNull('cashier_id')
            ->sum('profit');

        $thisMonthPlayerRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfMonth())
            ->whereNull('cashier_id')
            ->sum('profit');

        $thisWeekPlayerRevenue = Game::whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
            ->where('created_at', '>=', now()->startOfWeek())
            ->whereNull('cashier_id')
            ->sum('profit');

        $totalAgents = Agent::count();
        $totalBranches = Branch::count();

        $totalCashierGames = Game::whereNotNull('cashier_id')->count();
        $todayCashierGames = Game::whereNotNull('cashier_id')
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        $totalPlayerGames = Game::whereNull('cashier_id')->count();
        $todayPlayerGames = Game::whereNull('cashier_id')
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        $totalRegisteredPlayers = Player::count();
        $todayRegisteredPlayers = Player::where('created_at', '>=', now()->startOfDay())->count();

        return Inertia::render('Admin/Dashboard/Index', [
            'todayRevenue' => (int)$todayRevenue,
            'thisMonthRevenue' => (int)$thisMonthRevenue,
            'thisWeekRevenue' => (int)$thisWeekRevenue,
            'totalRevenue' => (int)$totalRevenue,
            'todayCashierRevenue' => (int)$todayCashierRevenue,
            'thisMonthCashierRevenue' => (int)$thisMonthCashierRevenue,
            'thisWeekCashierRevenue' => (int)$thisWeekCashierRevenue,
            'totalCashierRevenue' => (int)$totalCashierRevenue,
            'totalPlayerRevenue' => (int)$totalPlayerRevenue,
            'todayPlayerRevenue' => (int)$todayPlayerRevenue,
            'thisMonthPlayerRevenue' => (int)$thisMonthPlayerRevenue,
            'thisWeekPlayerRevenue' => (int)$thisWeekPlayerRevenue,
            'totalAgents' => $totalAgents,
            'totalBranches' => $totalBranches,
            'totalCashierGames' => $totalCashierGames,
            'todayCashierGames' => $todayCashierGames,
            'totalPlayerGames' => $totalPlayerGames,
            'todayPlayerGames' => $todayPlayerGames,
            'totalRegisteredPlayers' => $totalRegisteredPlayers,
            'todayRegisteredPlayers' => $todayRegisteredPlayers,
        ]);
    }

    public function users(Request $request): Response
    {
        $query = $request->input('search');

        $usersQuery = User::query();

        if ($query) {
            $usersQuery->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%');
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

    public function pwc()
    {
        $games = Game::where('is_tv_game', 1)->where('status', Game::STATUS_PENDING)->withCount('players', 'cartelas')->with('gameCategory', 'cashier.branch', 'pwc.cartela')->get();
        return Inertia::render('Admin/Games/PWC', [
            'games' => $games,
        ]);
    }

    public function addPwc(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string',
            'rows' => 'required|integer',
        ]);

        $game = Game::find($request->game_id);
        $cartela = Cartela::where('name', $request->name)->first();

        if (!$cartela) {
            return redirect()->back()->with('error', 'Cartela not found');
        }

        // Todo: Check if the cartela is added to the game
        PWC::updateOrCreate([
            'game_id' => $game->id,
        ], [
            'game_id' => $game->id,
            'cartela_id' => $cartela->id,
            'rows' => $request->rows,
        ]);
        return redirect()->back()->with('success', 'PWC added successfully');
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
