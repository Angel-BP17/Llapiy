<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'descripcion' => $this->faker->company,
            'abreviacion' => $this->faker->lexify('???'),
        ];
    }
}
