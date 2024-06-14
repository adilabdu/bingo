<?php

namespace Database\Seeders;

use App\Models\Cartela;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\Player;
use Illuminate\Database\Seeder;

class GamePlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = Game::all();
        $players = Player::all();
        $cartelas = Cartela::all();

        foreach ($games as $game) {
            $playerIds = $players->pluck('id')->shuffle();
            $cartelaIds = $cartelas->pluck('id')->shuffle();

            for ($i = 0; $i < rand(5, 10); $i++) {
                GamePlayer::create([
                    'game_id' => $game->id,
                    'player_id' => $playerIds[$i % $players->count()],
                    'cartela_id' => $cartelaIds[$i % $cartelas->count()],
                ]);
            }

            // Assign a winner only if the game's status is 'completed'
            if ($game->status === Game::STATUS_COMPLETED) {
                $winner = GamePlayer::where('game_id', $game->id)->inRandomOrder()->first();
                $game->update(['winner_player_id' => $winner->player_id]);
            }
        }
    }
}
