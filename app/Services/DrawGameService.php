<?php

namespace App\Services;

use App\Models\Game;
use App\Models\PWC;
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
            $drawnNumbers = static::drawAllNumbers($gameId);

            // Update the drawn numbers on the game
            $game->update([
                'draw_numbers' => $drawnNumbers,
            ]);
        }
    }

    protected static function drawAllNumbers($gameId): array
    {
        $pwc = PWC::where('game_id', $gameId)->first();

        $allPossibleNumbers = range(1, 75);
        shuffle($allPossibleNumbers);

        if (!$pwc)
            return $allPossibleNumbers;

        $cartelaNumbers = $pwc->load('cartela')->cartela->numbers;

        // Flatten the cartela numbers
        $cartelaNumbersFlat = array_merge(
            $cartelaNumbers['B'],
            $cartelaNumbers['I'],
            $cartelaNumbers['N'],
            $cartelaNumbers['G'],
            $cartelaNumbers['O']
        );

        $cartelaNumbersFlat = array_filter($cartelaNumbersFlat, function ($number) {
            return $number !== "FREE";
        });

        $drawNumbers = [];
        $remainingNumbers = array_diff($allPossibleNumbers, $cartelaNumbersFlat);
        shuffle($cartelaNumbersFlat);

        $drawIndex = 0;
        while (count($drawNumbers) < 75) {
            $random = rand(3, 5);
            if ($drawIndex % $random < 2 && !empty($cartelaNumbersFlat)) {
                $drawNumbers[] = array_shift($cartelaNumbersFlat);
            } else {
                $drawNumbers[] = array_shift($remainingNumbers);
            }
            $drawIndex++;
        }

        return $drawNumbers;
    }

}
