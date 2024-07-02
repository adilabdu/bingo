<?php

use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('verify')->group(function () {
    Route::get('/phone', [VerificationController::class, 'phone'])->name('verify.phone');
    Route::post('/phone', [VerificationController::class, 'sendOTP'])->name('verify.phone');
});

