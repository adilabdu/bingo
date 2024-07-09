<?php

use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('cashier/')->group(function () {
    Route::get('', [CashierController::class, 'index'])->name('cashier.index');
    Route::get('finance', [CashierController::class, 'finance'])->name('cashier.finance');
});

    Route::middleware(['auth', 'checkUserType:cashier'])->prefix('cashier/game')->group(function () {
    Route::get('/initiate', [CashierController::class, 'index'])->name('cashier.game.initiate');
    Route::get('/create/{gameCategoryId}', [CashierController::class, 'createGame'])->name('cashier.game.create');
    Route::post('/add', [CashierController::class, 'addPlayers'])->name('cashier.game.add');
    Route::get('/start/{cartelaName?}/{gameId?}', [CashierController::class, 'startGame'])->name('cashier.game.start');
});
