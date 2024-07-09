<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Determine if the login field is an email or phone number
        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        // Check if the user is blocked
        $user = User::where($field, $login)->first();

        if ($user && $user->is_blocked) {
            return back()->withErrors(['login' => 'Your account has been blocked.']);
        }

        // Attempt to authenticate the user
        $credentials = [$field => $login, 'password' => $request->input('password')];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'login' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();
        $userType = Auth::user()->type;

        return match ($userType) {
            User::TYPE_ADMIN => redirect()->intended('/admin'),
            User::TYPE_PLAYER => redirect()->intended('/game/initiate'),
            User::TYPE_CASHIER => redirect()->intended('/cashier/game/initiate'),
            default => redirect()->intended('/'),
        };
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
