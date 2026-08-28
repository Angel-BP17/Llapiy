<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GroupTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'descripcion' => $this->faker->jobTitle,
            'abreviacion' => $this->faker->lexify('???'),
        ];
    }
}
