<?php

namespace Database\Seeders;

use App\Models\Heatmap;
use Illuminate\Database\Seeder;

class HeatmapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return Heatmap::factory()->times(30)->create();
    }
}
