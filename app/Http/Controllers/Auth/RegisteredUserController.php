<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'email' => $request->type === 'admin' ? 'required|string|lowercase|email|max:255|unique:users,email' : 'nullable',
            'phone_number' => $request->type === 'player' ? 'required|regex:/(9)[0-9]{8}/|max:10|min:9|unique:users,phone_number' : 'nullable',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'type' => 'required|string|in:admin,player',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->type === 'admin' ? $request->email : null,
            'phone_number' => $request->type === 'player' ? $request->phone_number : null,
            'type' => $request->type,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return redirect()->route('register')->with('success', 'User registered successfully.');
    }
}
