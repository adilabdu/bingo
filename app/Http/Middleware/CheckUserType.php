<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */

    public function handle(Request $request, Closure $next, ...$userTypes)
    {
        // If the current user is not authenticated, return with
        // '401 Unauthorized' error.

        if (! auth()->check()) {
            abort(401, 'Access Denied!');
        }

        // Get the currently authenticated user
        $user = auth()->user();

        // Check the user type
        if (! in_array($user->type, $userTypes)) {

            if ($user->type === User::TYPE_PLAYER)
                return redirect()->route('game.initiate');

            abort(403, 'Access Denied!');

        }

        return $next($request);
    }
}
