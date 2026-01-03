<?php

namespace Database\Seeders;

use App\Models\BilliardTable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BilliardTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 31; $i++) {
            if ($i == 13) {
                continue;
            }
            BilliardTable::create([
                'number' => $i,
                'status' => 'available',
            ]);
        }
    }
}
