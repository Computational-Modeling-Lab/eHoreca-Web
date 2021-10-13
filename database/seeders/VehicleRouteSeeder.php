<?php

namespace Database\Seeders;

use App\Models\VehicleRoute;
use Illuminate\Database\Seeder;

class VehicleRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VehicleRoute::factory()->times(5)->create();
    }
}
