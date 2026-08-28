<?php

namespace Database\Factories;

use App\Models\DocumentarySeries;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentarySeries>
 */
class DocumentarySeriesFactory extends Factory
{
    protected $model = DocumentarySeries::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->bothify('##.##'),
            'nombre' => $this->faker->sentence(3),
        ];
    }
}
