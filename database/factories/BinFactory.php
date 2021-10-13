<?php

namespace Database\Factories;

use App\Models\Bin;
use Illuminate\Database\Eloquent\Factories\Factory;

class BinFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = ['compost','glass','recyclable','mixed','metal','paper','plastic'];
        $point = \Helper::instance()->horeca_request_with_point_from_latlng(array('lat' => $this->faker->randomFloat(4, 39.6, 39.8), 'lng' => $this->faker->randomFloat(4, 19.8, 20)));

        return [
            'location' => $point['location'],
            'capacity' => 2,
            'capacity_unit' => 'litres',
            'type' => $types[rand(0, 6)],
            'description' => $this->faker->text,
            'quantity' => $this->faker->randomDigit,
            'isPublic' => $this->faker->boolean(75),
        ];
    }
}
