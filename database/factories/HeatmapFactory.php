<?php

namespace Database\Factories;

use App\Models\Heatmap;
use Illuminate\Database\Eloquent\Factories\Factory;

class HeatmapFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Heatmap::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'user_id' => $this->faker->numberBetween(1,50),
            'valid_from' => $this->faker->dateTimeThisYear('now'),
            'valid_to' => $this->faker->dateTimeThisYear('+1 week'),
        ];
    }
}
