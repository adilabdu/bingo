<?php

namespace App\Services;

use App\Models\Player;

class SettleGameService
{
    public static function settleWinnerBalance($game): void
    {

        $player = Player::find($game->winner_player_id);

        $player->update([
            'balance' => $player->balance + $game->winner_net_amount
        ]);
    }
}
