<?php

namespace Database\Factories;

use App\Models\Pois;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PoisFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pois::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $point = \Helper::instance()->horeca_request_with_point_from_latlng(array('lat' => $this->faker->randomFloat(4,39.6, 39.8), 'lng' => $this->faker->randomFloat(4, 19.8, 20)));

        return [
            'title' => $this->faker->word,
            'description' => $this->faker->text,
            'location' => $point['location']
        ];
    }
}
