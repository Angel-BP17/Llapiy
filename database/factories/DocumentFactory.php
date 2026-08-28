<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'n_documento' => $this->faker->unique()->bothify('DOC-####'),
            'asunto' => $this->faker->sentence,
            'folios' => $this->faker->numberBetween(1, 100),
            'root' => null,
            'fecha' => $this->faker->date(),
            'periodo' => null,
            'user_id' => User::factory(),
            'document_type_id' => DocumentType::factory(),
            'group_id' => null,
            'subgroup_id' => null,
        ];
    }
}
