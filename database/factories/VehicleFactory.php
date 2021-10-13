<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = ['Φ.Ι.Χ. Ανοικτό', 'Φ.Ι.Χ. Κλειστό', 'Απορριμματοφόρο', 'Πλυντήριο κάδων', 'Σάρωθρο', 'Φ.Ι.Χ Τρίκυκλο', 'Φ.Ι.Χ. Ανατρεπόμενο', 'Φ.Ι.Χ. Βυτιοφόρο', 'Φ.Ι.Χ. Τράκτορας'];
        $municipality = ['ΚΕΡΚΥΡΑΙΩΝ', 'ΜΕΛΙΤΕΙΕΩΝ', 'ΘΙΝΑΛΙΩΝ', 'ΦΑΙΑΚΩΝ', 'ΕΣΠΕΡΙΩΝ', 'ΠΑΡΕΛΙΩΝ', 'ΑΧΙΛΛΕΙΩΝ', 'ΚΑΣΣΩΠΑΙΩΝ', 'ΠΑΛΑΙΟΚΑΣΤΡΙΤΩΝ', 'ΛΕΥΚΙΜΜΑΙΩΝ', 'ΑΓ. ΓΕΩΡΓΙΟΥ', 'ΚΟΡΙΣΣΙΩΝ'];
        $unity = ['Διοίκησης', 'Ηλεκτροφωτισμού', 'Καθαριότητας'];
        $in_service = [true, false];

        $letter_seed = str_split('ABEHIKMNOPTXYZ');
        $number_seed = str_split('0123456789');
        $plates = "";

        foreach (array_rand($letter_seed, 3) as $letter) {
            $plates .= $letter_seed[$letter];
        }

        foreach (array_rand($number_seed, 4) as $number) {
            $plates .= $number_seed[$number];
        }

        return [
            'internal_id' => 'test',
            'plates' => $plates,
            'type' => $type[rand(0, count($type) - 1)],
            'make' => 'Scania Mono Re Paok',
            'year_first_license' => rand(1930, 2002),
            "taxable_hp" => rand(75, 250),
            'payload' => rand(0, 200),
            'payload_unit' => 'litres',
            'municipality' => $municipality[rand(0, count($municipality) -1)],
            'unity' => $unity[rand(0, count($unity) -1)],
            'in_service' => $in_service[rand(0, 1)],
        ];
    }
}
