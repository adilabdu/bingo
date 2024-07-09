<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\GameController;
use App\Http\Middleware\EnsurePhoneNumberIsVerified;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'checkUserType:cashier'])->prefix('cashier/game')->group(function () {
    Route::get('/initiate', [CashierController::class, 'index'])->name('cashier.game.initiate');
    Route::get('/create/{gameCategoryId}', [CashierController::class, 'createGame'])->name('cashier.game.create');
    Route::post('/add', [CashierController::class, 'addPlayers'])->name('cashier.game.add');
});

