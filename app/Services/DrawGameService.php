<?php

namespace App\Services;

use App\Models\Game;

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
