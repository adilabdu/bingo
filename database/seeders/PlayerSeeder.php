<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::where('type', 'player')->get()->each(function ($user) {
            Player::create([
                'user_id' => $user->id,
                'balance' => rand(100, 1000),
            ]);
        });
    }
}
