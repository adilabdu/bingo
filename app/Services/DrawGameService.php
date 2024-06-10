<?php

namespace App\Services;

use App\Events\DrawGameEvent;
use App\Models\Game;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class DrawGameService
{
    public static function drawGame($gameId)
    {
        $game = Game::find($gameId);
        if (!$game) {
            return;
        }

        // Retrieve previously drawn numbers or initialize if none
        $drawnNumbers = $game->drawn_numbers ?? [];

        // Check if we need to draw numbers
        if (empty($drawnNumbers)) {
            // Draw all numbers since none have been drawn
            $drawnNumbers = static::drawAllNumbers();

            // Update the drawn numbers on the game
            $game->update([
                'draw_numbers' => $drawnNumbers,
                'status' => Game::STATUS_COMPLETED
            ]);
        }
    }

    protected static function drawAllNumbers(): array
    {
        $allPossibleNumbers = range(1, 75);
        shuffle($allPossibleNumbers);
        return $allPossibleNumbers;
    }

}
