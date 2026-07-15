<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Prize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prize>
 */
class PrizeFactory extends Factory
{
    protected $model = Prize::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'title' => fake()->sentence(2),
            'description' => fake()->sentence(),
            'winners_count' => 1,
            'rank' => fake()->numberBetween(1, 3),
        ];
    }
}
