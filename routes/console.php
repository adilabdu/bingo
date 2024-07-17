<?php

use App\Models\Game;
use App\Services\SettleGameService;
use App\Services\StartGameService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    // Check if active game has elapsed five minutes make the game status cancelled
    if (Game::where('status', Game::STATUS_ACTIVE)
        ->where('created_at', '<=', now()->subMinutes(5))
        ->exists()) {
        $game = Game::where('status', Game::STATUS_ACTIVE)
            ->where('created_at', '<=', now()->subMinutes(5))
            ->where('is_tv_game', false)
            ->first();
        if ($game)
        $game->update(['status' => Game::STATUS_CANCELLED]);
        SettleGameService::returnPlayerBalance($game);
    }

    StartGameService::checkGame();
 })->everySecond();
