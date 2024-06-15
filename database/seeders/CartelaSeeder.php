<?php

namespace Database\Seeders;

use App\Models\Cartela;
use Illuminate\Database\Seeder;

class CartelaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Cartela::create([
                'name' => 'Cartela ' . $i,
                'numbers' => json_encode(range(1, 15)),
            ]);
        }
    }
}
