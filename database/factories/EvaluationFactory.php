<?php

namespace Database\Factories;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluation>
 */
class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'judge_id' => User::factory()->judge(),
            'score' => fake()->numberBetween(0, 100),
            'notes' => fake()->sentence(),
            'status' => EvaluationStatus::Evaluated,
        ];
    }
}
