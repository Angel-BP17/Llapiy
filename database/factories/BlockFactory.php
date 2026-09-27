<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    protected $model = Block::class;

    public function definition(): array
    {
        return [
            'n_bloque' => null,
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
            'documentary_series_id' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Block $block) {
            if ($block->periods()->doesntExist()) {
                $block->periods()->create([
                    'rango_inicial' => is_numeric($block->rango_inicial) ? (int) $block->rango_inicial : 1,
                    'rango_final' => is_numeric($block->rango_final) ? (int) $block->rango_final : 10,
                    'periodo' => $block->periodo ?? ($block->fecha ? \Carbon\Carbon::parse($block->fecha)->year : now()->year),
                ]);
            }
        });
    }
}
