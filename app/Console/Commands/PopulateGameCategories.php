<?php

namespace App\Console\Commands;

use App\Models\GameCategory;
use Illuminate\Console\Command;

class PopulateGameCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-game-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the game categories table with some default data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Add some default game categories
        $gameCategories = [
            ['name' => 'Gold', 'amount' => 20, 'category' => 'basic'],
            ['name' => 'Platinum', 'amount' => 30, 'category' => 'basic'],
            ['name' => 'Diamond', 'amount' => 50, 'category' => 'basic'],
            ['name' => 'Silver', 'amount' => 70, 'category' => 'basic'],
            ['name' => 'Master', 'amount' => 100, 'category' => 'basic'],
            ['name' => 'Grandmaster', 'amount' => 200, 'category' => 'vip'],
            ['name' => 'Challenger', 'amount' => 500, 'category' => 'vip'],
            ['name' => 'Legend', 'amount' => 1000, 'category' => 'vip'],
            ['name' => 'Immortal', 'amount' => 2000, 'category' => 'vip'],
        ];

        foreach ($gameCategories as $gameCategory) {
            GameCategory::create($gameCategory);
        }

        $this->info('Game categories table populated successfully.');
    }
}
