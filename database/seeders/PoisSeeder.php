<?php

namespace Database\Seeders;

use App\Models\Pois;
use Illuminate\Database\Seeder;

class PoIsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pois::factory()->times(50)->create();
    }
}
