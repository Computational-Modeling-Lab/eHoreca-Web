<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // How many vehicles you need, defaulting to 10
        $count = (int)$this->command->ask('How many vehicles do you need ?', 10);

        $this->command->info("Creating {$count} vehicles.");

        // Create the vehicles
        Vehicle::factory()->times($count)->create();

        $this->command->info('Vehicles Created!');
    }
}
