<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin/')->group(function () {
    Route::get('', [AdminController::class, 'index'])->name('index');
    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::post('users/{id}/block', [UserController::class, 'block'])->name('users.block');
    Route::get('games', [AdminController::class, 'games'])->name('games');
    Route::get('games/{id}', [AdminController::class, 'game'])->name('game');
});
