<?php

namespace Database\Factories;

use App\Models\Andamio;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class AndamioFactory extends Factory
{
    protected $model = Andamio::class;

    public function definition(): array
    {
        return [
            'n_andamio' => $this->faker->numberBetween(1, 100),
            'descripcion' => $this->faker->sentence,
            'section_id' => Section::factory(),
        ];
    }
}
