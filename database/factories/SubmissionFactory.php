<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'participant_id' => User::factory()->participant(),
            'status' => SubmissionStatus::Submitted,
            'text_content' => fake()->paragraph(),
            'link_url' => null,
        ];
    }
}
