<?php

namespace Database\Seeders;

use App\Models\Tier;
use Illuminate\Database\Seeder;

class TierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tier::create(['name' => 'IRON', 'position' => 8]);
        Tier::create(['name' => 'BRONZE', 'position' => 7]);
        Tier::create(['name' => 'SILVER', 'position' => 6]);
        Tier::create(['name' => 'GOLD', 'position' => 5]);
        Tier::create(['name' => 'PLATINUM', 'position' => 4]);
        Tier::create(['name' => 'DIAMOND', 'position' => 3]);
        Tier::create(['name' => 'MASTER', 'position' => 2]);
        Tier::create(['name' => 'GRANDMASTER', 'position' => 1]);
        Tier::create(['name' => 'CHALLENGER', 'position' => 0]);
    }
}
