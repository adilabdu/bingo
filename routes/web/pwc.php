<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'checkUserType:super-admin', 'checkIfBlocked'])->prefix('admin/')->group(function () {
    Route::get('pwc', [AdminController::class, 'pwc'])->name('admin.pwc');
    Route::post('pwc', [AdminController::class, 'addPwc'])->name('admin.add.pwc');
});
