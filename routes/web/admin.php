<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'checkUserType:admin', 'checkIfBlocked'])->prefix('admin/')->group(function () {
    Route::get('', [AdminController::class, 'index'])->name('admin.index');
    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::post('users/{id}/block', [UserController::class, 'block'])->name('users.block');
    Route::get('games', [AdminController::class, 'games'])->name('games');
    Route::get('games/{id}', [AdminController::class, 'game'])->name('game');
    Route::get('users/register', [AdminController::class, 'register'])->name('users.register');
    Route::get('users/{userId}', [AdminController::class, 'player'])->name('users.player');
    Route::get('profile', [AdminController::class, 'profile'])->name('admin.profile.edit');
});
