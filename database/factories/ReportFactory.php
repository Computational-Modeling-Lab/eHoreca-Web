<?php

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $issues = ['bin full','bin almost full','bin damaged','bin missing'];
        $point = \Helper::instance()->horeca_request_with_point_from_latlng(array('lat' => $this->faker->randomFloat(4,39.6, 39.8), 'lng' => $this->faker->randomFloat(4, 19.8, 20)));

        $w_prod_id = null;
        if (rand(1,100) > 50)
            $w_prod_id = rand(1,20);

        return [
            'bin_id' => $this->faker->numberBetween(1, 50),
            'user_id' => $this->faker->numberBetween(1,50),
            'location' => $point['location'],
            'location_accuracy' => $this->faker->numberBetween(0,3),
            'issue' => $issues[rand(0,3)],
            'comment' => $this->faker->text(255),
            'report_photos_filenames' => json_encode($this->faker->words(rand(0,5))),   // image($dir, $width, $height, 'cats') when $dir != null, https://github.com/fzaninotto/Faker#fakerproviderimage,
            'w_producer_id' => $w_prod_id
        ];
    }
}
