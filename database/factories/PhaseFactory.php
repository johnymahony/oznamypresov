<?php

namespace Database\Factories;

use App\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhaseFactory extends Factory
{
    protected $model = Phase::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'project_id' => 1,
        ];
    }
}
