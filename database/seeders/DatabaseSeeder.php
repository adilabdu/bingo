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

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => '0912345678',
        ]);

        $user->player()->create([
            'balance' => 1000,
        ]);

        $user = User::factory()->create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'phone_number' => '0911223344',
        ]);

        $user->player()->create([
            'balance' => 0,
        ]);
    }
}
