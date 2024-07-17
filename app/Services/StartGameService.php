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
            ->where('is_tv_game', false)
            ->first();

        if ($game) {
            $game->load(['players', 'gameCategory']);

            if ($game->players()->count() < 2) {
                StartGameEvent::dispatch($game, false);
                SettleGameService::returnPlayerBalance($game);
                $game->update(['status' => Game::STATUS_CANCELLED]);
                return;
            }

            StartGameEvent::dispatch($game, true);

            $totalPlayers = $game->players()->count();

            $game->update(['winner_net_amount' => $totalPlayers * (int)$game->gameCategory->amount * config('WINNER_RETAIN_PERCENTAGE', 0.85) ]);
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
