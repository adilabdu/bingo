<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PlayerSeeder::class,
            GameCategorySeeder::class,
            CartelaSeeder::class,
            GameSeeder::class,
            GamePlayerSeeder::class,
        ]);
    }
}
