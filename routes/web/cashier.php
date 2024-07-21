<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\CashierGameController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('cashier/')->group(function () {
    Route::get('', [CashierController::class, 'index'])->name('cashier.index');
    Route::get('finance', [CashierController::class, 'finance'])->name('cashier.finance');
    Route::post('', [CashierController::class, 'store'])->name('cashier.store');
});

Route::middleware(['auth', 'checkUserType:cashier'])->prefix('cashier/game')->group(function () {
    Route::get('/initiate', [CashierGameController::class, 'index'])->name('cashier.game.initiate');
    Route::get('/create/{gameCategoryId}', [CashierGameController::class, 'createGame'])->name('cashier.game.create');
    Route::post('/add', [CashierGameController::class, 'addPlayers'])->name('cashier.game.add');
    Route::get('/start/{cartelaName?}/{gameId?}/{gameCategoryId?}', [CashierGameController::class, 'startGame'])->name('cashier.game.start');
    Route::post('/finish', [CashierGameController::class, 'finish'])->name('cashier.game.finish');
});
