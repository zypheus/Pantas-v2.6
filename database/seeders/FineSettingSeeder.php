<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FineSetting;

class FineSettingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $defaults = FineSetting::defaultAttributes();

        FineSetting::firstOrCreate(
            ['effective_from' => $defaults['effective_from']],
            $defaults
        );
    }
}
