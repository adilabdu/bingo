<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameCategory;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = GameCategory::all();

        for ($i = 1; $i <= 10; $i++) {
            Game::create([
                'game_category_id' => $categories->random()->id,
                'status' => ['pending', 'active', 'completed'][array_rand(['pending', 'active', 'completed'])],
                'scheduled_at' => now()->addDays(rand(1, 10)),
                'winner_net_amount' => rand(500, 2000),
                'draw_numbers' => json_encode(array_rand(range(1, 15), 5)),
                'winning_numbers' => json_encode(array_rand(range(1, 15), 3)),
            ]);
        }
    }
}
