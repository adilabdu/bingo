<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

}
