<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 4) as $i) {
            Court::create([
                'name' => "Lapangan {$i}",
                'price_per_hour' => 50000,
                'is_active' => true,
            ]);
        }
    }
}
