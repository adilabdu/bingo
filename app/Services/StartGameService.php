<?php
namespace App\Services;

use App\Events\StartGameEvent;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class StartGameService
{
    public static function checkGame(){
        $game = Game::where('scheduled_at', '<=', now())
            ->where('status', Game::STATUS_PENDING)
            ->first();

        if ($game) {
            StartGameEvent::dispatch($game);

            $totalPlayers = $game->players()->count();

            // Todo: Change the percentage value to .env variable
            $game->update(['winner_net_amount' => $totalPlayers * (int)$game->gameCategory->amount * 0.9]);
            $game->update(['status' => Game::STATUS_ACTIVE]);

            if (!$game->draw_numbers) {
                DrawGameService::drawGame($game->id);
            }
        }
    }

    public static function getDrawnNumbersForBatch($game, $batchIndex): array
    {
        $batchSize = 10;
        $start = $batchIndex * $batchSize;
        return array_slice($game->draw_numbers ?? [], $start, $batchSize);
    }


}
