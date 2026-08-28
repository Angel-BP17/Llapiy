<?php

namespace Database\Factories;

use App\Models\Andamio;
use App\Models\Box;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoxFactory extends Factory
{
    protected $model = Box::class;

    public function definition(): array
    {
        return [
            'n_box' => $this->faker->unique()->bothify('BOX-###'),
            'andamio_id' => Andamio::factory(),
        ];
    }
}
