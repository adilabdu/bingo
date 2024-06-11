<?php

namespace App\Traits;

use App\Models\Cartela;

trait ValidatesWinningNumbers
{

    // Define the desired order of keys
    const ORDER = ["B", "I", "N", "G", "O"];

    public static function winnerRowsFound(array $winningNumbers, Cartela $cartela): int
    {
        $rowsDetected = 0;

        foreach (self::winnerRows($cartela) as $row) {
            if (count(array_intersect($row, $winningNumbers)) === 5) {
                $rowsDetected++;
            }
        }

        return $rowsDetected;
    }

    public static function winnerColumnsFound(array $winningNumbers, Cartela $cartela): int
    {
        $columnsDetected = 0;

        foreach (self::winnerColumns($cartela) as $column) {
            if (count(array_intersect($column, $winningNumbers)) === 5) {
                $columnsDetected++;
            }
        }

        return $columnsDetected;
    }

    public static function winnerDiagonalsFound(array $winningNumbers, Cartela $cartela): int
    {
        $diagonalsDetected = 0;

        foreach (self::winnerDiagonals($cartela) as $diagonal) {
            if (count(array_intersect($diagonal, $winningNumbers)) === 5) {
                $diagonalsDetected++;
            }
        }

        return $diagonalsDetected;
    }

    public static function winnerCornerFound(array $winningNumbers, Cartela $cartela): int
    {
        $cornersDetected = 0;

        if (count(array_intersect(self::winnerCorner($cartela), $winningNumbers)) === 4) {
            $cornersDetected++;
        }

        return $cornersDetected;
    }

    private static function winnerColumns(Cartela $cartela): array
    {
        $columns = [];

        foreach (self::ORDER as $key) {
            $columns[] = $cartela->numbers[$key];
        }

        return $columns;
    }

    private static function winnerRows(Cartela $cartela): array
    {
        return array_map(null, ...self::winnerColumns($cartela));
    }

    private static function winnerDiagonals(Cartela $cartela): array
    {
        $board = self::winnerColumns($cartela);
        $min = 0;
        $max = 4;
        $forwardDiagonal = [];
        $backwardDiagonal = [];

        for ($i = $min; $i <= $max; $i++) {
            $forwardDiagonal[] = $board[$i][$i];
            $backwardDiagonal[] = $board[$i][4 - $i];
        }

        return [$forwardDiagonal, $backwardDiagonal];
    }

    private static function winnerCorner(Cartela $cartela): array
    {
        $board = self::winnerColumns($cartela);
        $corners = [];

        $corners[] = $board[0][0];
        $corners[] = $board[0][4];
        $corners[] = $board[4][0];
        $corners[] = $board[4][4];

        return $corners;
    }
}
