<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class AgentController extends Controller
{
    public function index()
    {
        $agent = auth()->user()->agent;
        $branches = $agent?->branches()->with('cashiers', 'transactions')->get();

        // Calculate top-performing branches based on the number of transactions
        $topBranches = $branches->sortByDesc(function ($branch) {
            return $branch->transactions->count();
        })->take(3);

        // Fetch recent activities
        $recentActivities = Activity::causedBy(auth()->user())->latest()->take(5)->get();

        // Total revenue of all branches
        $totalRevenue = $branches->sum(function ($branch) {
            return $branch->transactions->sum('amount');
        });

        // Today's revenue
        $todayRevenue = $branches->sum(function ($branch) {
            return $branch->transactions->where('created_at', '>=', now()->startOfDay())->sum('amount');
        });

        // This month's revenue
        $thisMonthRevenue = $branches->sum(function ($branch) {
            return $branch->transactions->where('created_at', '>=', now()->startOfMonth())->sum('amount');
        });

        // This week's revenue
        $thisWeekRevenue = $branches->sum(function ($branch) {
            return $branch->transactions->where('created_at', '>=', now()->startOfWeek())->sum('amount');
        });

        return Inertia::render('Agent/Index', [
            'agent' => $agent,
            'branches' => $branches,
            'recentActivities' => $recentActivities,
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
            'thisMonthRevenue' => $thisMonthRevenue,
            'thisWeekRevenue' => $thisWeekRevenue,
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

