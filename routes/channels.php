<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('start-game', function ($user) {
    return Auth::check();
});

Broadcast::channel('draw-game', function () {
    return Auth::check();
});

Broadcast::channel('game-result', function () {
    return Auth::check();
});
