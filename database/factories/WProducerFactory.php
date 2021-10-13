<?php

namespace Database\Factories;

use App\Models\WProducer;
use Illuminate\Database\Eloquent\Factories\Factory;

class WProducerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WProducer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $randomUsers = array();
        $randomBins = array();

        for ($i = 0; $i < rand(1, 5); $i += 1)
        {
            $randomUsers []= $this->faker->unique(true)->numberBetween(1, 50);
        }

        for ($i = 0; $i < rand(1, 5); $i += 1)
        {
            $randomBins []= $this->faker->unique(true)->numberBetween(1, 50);
        }

        $point = \Helper::instance()->horeca_request_with_point_from_latlng(array('lat' => $this->faker->randomFloat(4,39.6, 39.8), 'lng' => $this->faker->randomFloat(4, 19.8, 20)));

        $pin = sprintf("%04s", rand(0, 9999));

        return [
            'title' => $this->faker->company,
            'contact_name' => $this->faker->name,
            'join_pin' => $pin,
            'contact_telephone' => $this->faker->tollFreePhoneNumber,
            'location' => $point['location'],
            'description' => $this->faker->realText(rand(100, 250)),
            'users' => json_encode($randomUsers),
            'bins' => json_encode($randomBins),
        ];
    }
}
