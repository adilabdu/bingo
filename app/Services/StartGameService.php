<?php
namespace App\Services;

use App\Events\StartGameEvent;
use App\Models\Game;

class StartGameService
{
    public static function checkGame(){
        $game = Game::where('scheduled_at', '<=', now())
            ->first();

        if ($game) {
            StartGameEvent::dispatch($game);

            // calculate total players, and update winning amount
            $totalPlayers = $game->players()->count();

            $game->update([
                'winner_net_amount' => $totalPlayers * (int)$game->gameCategory->amount * 0.9
            ]);
            $game->update(['status' => Game::STATUS_ACTIVE]);

        }
    }
}
