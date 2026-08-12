<?php

namespace Database\Factories;

use App\Cityzen;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityzenFactory extends Factory
{
    protected $model = Cityzen::class;

    public function definition(): array
    {
        return [
            'email' => fake()->email(),
            'receive_type' => Cityzen::DAILY,
            'radius' => 1000,
        ];
    }
}
