<?php

namespace Database\Factories;

use App\Models\AreaGroupType;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'area_group_type_id' => 1, // Se sobrescribe en los tests
            'descripcion' => $this->faker->word,
            'abreviacion' => $this->faker->lexify('???'),
        ];
    }
}
