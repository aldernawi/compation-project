<?php

namespace Database\Factories;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('now', '+1 month');

        return [
            'organizer_id' => User::factory()->organizer(),
            'competition_type_id' => CompetitionType::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'terms' => fake()->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => fake()->dateTimeBetween($startsAt, '+2 months'),
            'status' => CompetitionStatus::Upcoming,
            'requires_approval' => true,
            'evaluation_method' => 'average_score',
        ];
    }
}
