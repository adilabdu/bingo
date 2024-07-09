<?php


use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('cashier/')->group(function () {
    Route::get('', [CashierController::class, 'index'])->name('cashier.index');
    Route::get('finance', [CashierController::class, 'finance'])->name('cashier.finance');
});
