<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function block($id)
    {
        $user = User::find($id);
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $message = $user->is_blocked ? 'User blocked successfully.' : 'User unblocked successfully.';

        return redirect()->back()->with('success', $message);
    }
}
