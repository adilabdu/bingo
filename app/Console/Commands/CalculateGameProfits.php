<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateGameProfits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-game-profits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Calculate the profit of each game
        $games = Game::where('status', Game::STATUS_COMPLETED)->with('gameCategory','players')->get();
        $games->each(function ($game) {
            $game->update([
                'profit' => ($game->gameCategory->amount * $game->players()->count()) - $game->winner_net_amount
            ]);
        });

        $this->info('Game profits calculated successfully');
    }
}
