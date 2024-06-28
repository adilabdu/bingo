<?php
namespace App\Services;



use App\Events\GamePlayersEvent;
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

        $player = auth()->user()->load('player')->player;

        // Check if the player has an active game
        $activeGame = GamePlayer::where('player_id', $player->id)
            ->whereHas('game', function ($query) {
                $query->whereIn('status', [Game::STATUS_ACTIVE,Game::STATUS_PENDING]);
            })
            ->first();

        if ($activeGame) {
            return $activeGame->game;
        }

        if (!$game) {
            $game = self::createGame($gameCategoryId);
        }

        $game->load('gameCategory');

        self::joinGame($game, $player->id, $cartelaId);

        $totalPlayers = $game->players()->count();
        GamePlayersEvent::dispatch($totalPlayers, $game);

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
