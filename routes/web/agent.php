<?php

use App\Http\Controllers\AgentController;
use Illuminate\Support\Facades\Route;

Route::prefix('agent/')->middleware('auth')->group(function () {
    Route::get('', [AgentController::class, 'index'])->name('agent.home');
    Route::get('branches', [AgentController::class, 'branches'])->name('agent.branches');
    Route::post('branches', [AgentController::class, 'storeBranch'])->name('agent.branches.store');
    Route::post('toggle', [AgentController::class, 'toggle'])->name('agent.toggle');
});
