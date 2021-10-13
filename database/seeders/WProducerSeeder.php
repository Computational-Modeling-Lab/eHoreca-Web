<?php

namespace Database\Seeders;

use App\Models\WProducer;
use Illuminate\Database\Seeder;

class WProducerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WProducer::factory()->times(20)->create();
    }
}
