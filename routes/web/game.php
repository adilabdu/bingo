<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('game')->group(function () {
    Route::get('/initiate', [GameController::class, 'index'])->name('game.initiate');
    Route::get('/initiate/{id}', [GameController::class, 'selectCartela'])->name('game.cartela');
    Route::get('/', [GameController::class, 'playGame'])->name('game.play');
});

