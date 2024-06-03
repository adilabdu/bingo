<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('game')->group(function () {
    Route::get('/initiate', [GameController::class, 'index'])->name('game.index');
    Route::get('/initiate/1', [GameController::class, 'selectCartela'])->name('game.cartela');
});

