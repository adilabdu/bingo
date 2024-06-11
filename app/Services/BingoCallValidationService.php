<?php

namespace App\Services;

use App\Models\Cartela;
use App\Models\Game;
use App\Traits\ValidatesWinningNumbers;

class BingoCallValidationService
{
    use ValidatesWinningNumbers;

    public static function validate(Game $game, Cartela $cartela, array $possibleWinningNumbers, int $cutOff): bool
    {
        // 1. Get the possible winning numbers
        // 2. Compare the possible winning numbers with the drawn numbers
        // 3. If the possible winning numbers are in the drawn numbers, continue
        // 4. Check the possible winning numbers make a Bingo
        // 5. If the possible winning numbers make a Bingo, return true

        return
            self::validateNumbersAgainstDrawNumbers($possibleWinningNumbers, $game->draw_numbers, $cutOff) &&
            self::validateNumbersAgainstCartela($possibleWinningNumbers, $cartela);
    }

    private static function validateNumbersAgainstCartela(array $possibleWinningNumbers, Cartela $cartela): bool
    {
        $rowsDetected = self::winnerRowsFound($possibleWinningNumbers, $cartela);
        $columnsDetected = self::winnerColumnsFound($possibleWinningNumbers, $cartela);
        $diagonalsDetected = self::winnerDiagonalsFound($possibleWinningNumbers, $cartela);
        $cornersDetected = self::winnerCornerFound($possibleWinningNumbers, $cartela);
        $completed = $rowsDetected + $columnsDetected + $diagonalsDetected + $cornersDetected;

        if ($cornersDetected > 0) {
            return true;
        }

        return $completed > 1;
    }

    private static function validateNumbersAgainstDrawNumbers(array $possibleWinningNumbers, array $drawnNumbers, int $cutOff): bool
    {
        // 1. Slice the drawn numbers to the length of cutOff
        // 2. Compare the sliced drawn numbers with the possible winning numbers
        // 3. If the sliced drawn numbers are equal to the possible winning numbers, return true

        $slicedDrawnNumbers = array_slice($drawnNumbers, 0, $cutOff);
        return empty(array_diff($possibleWinningNumbers, $slicedDrawnNumbers));
    }
}
