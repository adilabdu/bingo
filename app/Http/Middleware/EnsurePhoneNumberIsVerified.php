<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneNumberIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user->phone_verified_at === null) {
            return redirect()->route('verify.phone');
        }

        return $next($request);
    }
}
