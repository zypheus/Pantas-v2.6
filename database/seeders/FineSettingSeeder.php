<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FineSetting;

class FineSettingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        FineSetting::create([
            'fine_per_day' => 5.00,
            'max_fine' => 500.00,
            'grace_period_days' => 0,
            'effective_from' => now(),
        ]);
    }
}
