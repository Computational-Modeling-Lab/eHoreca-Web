<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Technical',
            'surname' => 'Administrator',
            'email' => 'tech@cwa.gr',
            'username' => 'eHorecaAdmin',
            'password' => Hash::make(env('ADMIN_PASS')),
            'details' => 'Ionian University CMoD Lab account for IT support',
            'role' => 'admin',
        ]);

        // User::factory()->times(50)->create();
    }
}
