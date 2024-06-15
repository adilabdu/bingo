<?php

use App\Http\Controllers\WalletController;

Route::middleware('auth')->prefix('wallet')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');
});

