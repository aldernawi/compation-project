<?php

namespace Database\Factories;

use App\Enums\SubmissionKind;
use App\Models\CompetitionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CompetitionType>
 */
class CompetitionTypeFactory extends Factory
{
    protected $model = CompetitionType::class;

    public function definition(): array
    {
        $name = fake()->unique()->sentence(2);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'submission_kind' => fake()->randomElement(SubmissionKind::cases()),
        ];
    }
}
