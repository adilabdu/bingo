<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Agent;
use App\Models\Cashier;
use App\Models\Player;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|regex:/^\+251[79][0-9]{8}$/|max:13|unique:users,phone_number',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'type' => 'required|string|in:admin,player,cashier,agent',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'type' => $request->type,
            'password' => Hash::make($request->password),
        ]);

        match ($request->type) {
            'admin' => Admin::create(['user_id' => $user->id]),
            'player' => Player::create(['user_id' => $user->id, 'balance' => env('INITIAL_PLAYER_BONUS_BALANCE', 0)]),
            'cashier' => Cashier::create(['user_id' => $user->id, 'branch_id' => $request->branch_id, 'balance' => 0]),
            'agent' => Agent::create(['user_id' => $user->id]),
        };
        event(new Registered($user));

        if (auth()->check()) {
            // If a logged-in user is registering another user
            return redirect()->back()->with('success', ucfirst($request->type) . ' registered successfully.');
        } else {
            // If a guest is registering
            auth()->login($user);

            if ($user->type == 'cashier') {
                return redirect()->route('agent.branches')->with('success', 'Cashier registered successfully.');
            } elseif ($user->type == 'admin') {
                return redirect()->route('index')->with('success', 'Admin registered successfully.');
            } elseif ($user->type == 'agent') {
                return redirect()->route('agent.index')->with('success', 'Agent registered successfully.');
            } else {
                return redirect()->route('game.initiate')->with('success', 'User registered successfully.');
            }
        }
    }
}
