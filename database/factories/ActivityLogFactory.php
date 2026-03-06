<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'model' => 'App\\Models\\Document',
            'action' => $this->faker->randomElement(['CREATED', 'UPDATED', 'DELETED']),
            'before' => json_encode(['name' => 'Old Name']),
            'after' => json_encode(['name' => 'New Name']),
        ];
    }
}
