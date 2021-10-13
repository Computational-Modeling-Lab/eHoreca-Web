<?php

namespace Database\Factories;

use App\Models\VehicleRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleRouteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VehicleRoute::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vehicle_id' => $this->faker->unique(true)->numberBetween(1, 20),
            'route_id' => $this->faker->unique(true)->numberBetween(1, 30),
            'route_completed' => false,
            'concluded_at_bin_id' => 0
        ];
    }
}
