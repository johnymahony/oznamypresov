<?php

namespace Database\Factories;

use App\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
    protected $model = Board::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'published_at' => fake()->dateTime(),
            'status' => 'new',
            'category_id' => 1,
        ];
    }
}
