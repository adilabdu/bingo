<?php

use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'checkUserType:player', 'checkIfBlocked'])->prefix('wallet')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');
});

