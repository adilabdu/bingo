<?php

namespace Database\Seeders;

use App\Models\GameCategory;
use Illuminate\Database\Seeder;

class GameCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Category 1', 'amount' => 100, 'category' => 'basic'],
            ['name' => 'Category 2', 'amount' => 200, 'category' => 'advanced'],
            ['name' => 'Category 3', 'amount' => 300, 'category' => 'expert'],
        ];

        foreach ($categories as $category) {
            GameCategory::create($category);
        }
    }
}
