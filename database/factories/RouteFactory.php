<?php

namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Route::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $randomNumbers = array();

        for ($i = 0; $i < rand(3, 10); $i += 1)
        {
            $randomNumbers []= $this->faker->unique(true)->numberBetween(1, 50);
        }

        return [
            'user_id' => $this->faker->numberBetween(1,50),
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'bins' => json_encode($randomNumbers),
            'outcome' => null,
        ];
    }
}
