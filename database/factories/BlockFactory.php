<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    protected $model = Block::class;

    public function definition(): array
    {
        return [
            'n_bloque' => $this->faker->unique()->bothify('BLK-####'),
            'asunto' => $this->faker->sentence,
            'folios' => $this->faker->numberBetween(1, 100),
            'rango_inicial' => $this->faker->numerify('####'),
            'rango_final' => $this->faker->numerify('####'),
            'root' => null,
            'fecha' => $this->faker->date(),
            'periodo' => $this->faker->year,
            'user_id' => User::factory(),
            'group_id' => null,
            'subgroup_id' => null,
            'box_id' => null,
        ];
    }
}
