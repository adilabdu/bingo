<?php

use App\Http\Controllers\Chapa\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('chapa')->group(function () {
    // Route for initiating payment (deposit)
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');

    // Route for handling payment callback
    Route::get('/payment/callback/{transactionId}', [PaymentController::class, 'handleCallback'])->name('payment.callback');

    // Routes for handling payment success and failure
//    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failure', [PaymentController::class, 'paymentFailure'])->name('payment.failure');

    // Route for initiating and handling callback withdrawal
    Route::post('/withdraw/initiate', [PaymentController::class, 'initiateWithdrawal'])->name('withdraw.initiate');
    Route::get('/withdrawal/callback/{transactionId}', [PaymentController::class, 'handleWithdrawalCallback'])->name('withdrawal.callback');

    // Routes for handling withdrawal success and failure
    Route::get('/withdraw/success', [PaymentController::class, 'withdrawalSuccess'])->name('withdraw.success');
    Route::get('/withdraw/failure', [PaymentController::class, 'withdrawalFailure'])->name('withdraw.failure');
});
