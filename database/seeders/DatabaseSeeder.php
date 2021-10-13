<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            // BinSeeder::class,
            // HeatmapSeeder::class,
            // RouteSeeder::class,
            // ReportSeeder::class,
            // VehicleSeeder::class,
            // PoisSeeder::class,
            // WProducerSeeder::class,
            // VehicleRouteSeeder::class,
        ]);
    }
}
