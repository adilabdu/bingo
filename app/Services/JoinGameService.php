<?php
namespace App\Services;



use App\Models\Game;
use App\Models\GamePlayer;
use Carbon\Carbon;

class JoinGameService
{
    public static function startGame($cartelaId, $gameCategoryId)
    {
        $game = Game::where('game_category_id', $gameCategoryId)
            ->where('status', 'pending')
            ->first();

        $player = auth()->user()->load('player');

        if (!$game) {
            $game = self::createGame($gameCategoryId);
        }

        $game->load('gameCategory');

        self::joinGame($game, $player->id, $cartelaId);

        return $game;
    }

    private static function createGame($gameCategoryId)
    {

        return Game::create([
            'game_category_id' => $gameCategoryId,
            'status' => Game::STATUS_PENDING,
            'scheduled_at' => Carbon::now()->addSeconds((int)env('GAME_WAITING_TIME'))
        ]);
    }

    private static function joinGame($game, $playerId, $cartelaId): void
    {
        $gamePlayer = GamePlayer::where('game_id', $game->id)
            ->where('player_id', $playerId)
            ->first();

        if ($gamePlayer) {
            return;
        }

        GamePlayer::create([
            'game_id' => $game->id,
            'player_id' => $playerId,
            'cartela_id' => $cartelaId
        ]);

        self::settlePlayerBalance($game);
    }

    private static function settlePlayerBalance($game): void
    {
        auth()->user()->player->update([
            'balance' => auth()->user()->player->balance - $game->gameCategory->amount
        ]);
    }
}
