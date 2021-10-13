<?php

namespace Database\Seeders;

use App\Models\waste;
use Illuminate\Database\Seeder;

class WastesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['compost', 'glass', 'recyclable', 'mixed', 'metal', 'paper', 'plastic'];

        foreach ($types as $type)
        {
            waste::factory()
                ->create(
                    ['type' => $type]
                );
        }
    }
}
