<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $this->call([
//            UserSeeder::class,
//            PlayerSeeder::class,
//            GameCategorySeeder::class,
//            CartelaSeeder::class,
//            GameSeeder::class,
//            GamePlayerSeeder::class,
        // User::factory(10)->create();

        Artisan::call('app:populate-cartelas');
        Artisan::call('app:populate-game-categories');

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
