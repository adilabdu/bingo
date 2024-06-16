<?php

use App\Http\Controllers\GameController;

Route::middleware('auth')->prefix('game')->group(function () {
    Route::get('/initiate', [GameController::class, 'index'])->name('game.initiate');
    Route::get('/initiate/{categoryId}/{cartelaName?}', [GameController::class, 'selectCartela'])->name('game.cartela');
    Route::get('/play', [GameController::class, 'playGame'])->name('game.play');
    Route::post('/play/bingo/{cartela}/{game}', [GameController::class, 'callBingo'])->name('game.bingo.call');
    Route::get('/join', [GameController::class, 'joinGame'])->name('game.join');
});

