<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class AgentController extends Controller
{
    public function index()
    {
        $agent = auth()->user()->agent;
        $branches = $agent?->branches()
            ->with('cashiers', 'transactions')
            ->withCount('cashiers')
            ->get();


        // Fetch recent activities
        $recentActivities = Activity::causedBy(auth()->user())->latest()->take(5)->get();

        // Total revenue of all branches
        $totalRevenue = $branches->sum(function ($branch) {
            return $branch->cashiers->load('games')->sum(function ($cashier) {
                return $cashier->games
                    ->where('status', Game::STATUS_COMPLETED)
                    ->sum('profit');
            });
        });

        // Today's revenue
        $todayRevenue = $branches->sum(function ($branch) {
            return $branch->cashiers->load('games')->sum(function ($cashier) {
                return $cashier->games->whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
                    ->where('created_at' ,'>=', now()->startOfDay())
                    ->sum('profit');
            });
        });

        // This month's revenue
        $thisMonthRevenue = $branches->sum(function ($branch) {
            return $branch->cashiers->load('games.gameCategory')->sum(function ($cashier) {
                return $cashier->games->whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('profit');
            });
        });

        // This week's revenue
        $thisWeekRevenue = $branches->sum(function ($branch) {
            return $branch->cashiers->load('games.gameCategory')->sum(function ($cashier) {
                return $cashier->games->whereIn('status', [Game::STATUS_COMPLETED, Game::STATUS_ACTIVE])
                    ->where('created_at', '>=', now()->startOfWeek())
                    ->sum('profit');
            });
        });

        // Total Game
        $totalGames = $branches->sum(function ($branch) {
            return $branch->cashiers->load('games')->sum(function ($cashier) {
                return $cashier->games->count();
            });
        });

        // Active Game
        $activeGames = $branches->sum(function ($branch) {
            return $branch->cashiers->load('games')->sum(function ($cashier) {
                return $cashier->games->whereIn('status', [Game::STATUS_ACTIVE, Game::STATUS_PENDING])->count();
            });
        });

        return Inertia::render('Agent/Index', [
            'agent' => $agent,
            'branches' => $branches,
            'recentActivities' => $recentActivities,
            'totalRevenue' => (int)$totalRevenue,
            'todayRevenue' => (int)$todayRevenue,
            'thisMonthRevenue' => (int)$thisMonthRevenue,
            'thisWeekRevenue' => (int)$thisWeekRevenue,
            'totalGames' => $totalGames,
            'activeGames' => $activeGames,
        ]);
    }
    public function branches()
    {
        $agent = auth()->user()->agent;

        // Get branches with their cashiers
        $branches = Branch::where('agent_id', $agent->id)
            ->with('cashiers.user')
            ->get();

        return Inertia::render('Agent/Branches', [
            'branches' => $branches,
        ]);
    }

    public function storeBranch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $agent = auth()->user()->agent;

        $branch = Branch::create([
            'agent_id' => $agent->id,
            'name' => $request->name,
            'location' => $request->location,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($branch)
            ->log('created a branch');

        return redirect()->route('agent.branches')->with('success', 'Branch created successfully.');
    }
}

