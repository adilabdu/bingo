<?php

namespace App\Console\Commands;

use App\Models\Cartela;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateCartelas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-cartelas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the cartelas table with random data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('cartelas')->delete(); // Optional: Clear the table on each run

        $rangeMap = [
            'B' => [1, 15],
            'I' => [16, 30],
            'N' => [31, 45],
            'G' => [46, 60],
            'O' => [61, 75]
        ];

        $cartelas = [];
        $cartelaCount = 100; // Define how many cartelas you want to generate

        for ($i = 1; $i <= $cartelaCount; $i++) {
            $numbers = [];
            foreach ($rangeMap as $letter => $range) {
                // 'N' has one less number to account for the free space in traditional bingo
                $count = $letter === 'N' ? 4 : 5;
                $numbers[$letter] = $this->generateUniqueNumbers($range, $count, $cartelas);
            }

            $cartelas[] = $numbers;
            DB::table('cartelas')->insert([
                'name' => (string) $i,
                'numbers' => json_encode($numbers),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->info('Cartelas generated successfully.');
    }

    private function generateUniqueNumbers($range, $count, $existingCartelas)
    {
        $numbers = [];

        while (count($numbers) < $count) {
            $number = rand($range[0], $range[1]);
            if (!in_array($number, $numbers)) {
                $numbers[] = $number;
            }
        }

        return $numbers;
    }
}
