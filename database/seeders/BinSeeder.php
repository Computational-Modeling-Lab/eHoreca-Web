<?php

namespace Database\Seeders;

use App\Models\Bin;
use Illuminate\Database\Seeder;

class BinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bin::factory()->times(50)->create();
    }
}
