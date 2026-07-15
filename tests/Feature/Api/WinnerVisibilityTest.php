<?php

use App\Models\Competition;
use App\Models\Evaluation;
use App\Models\Prize;
use App\Models\Submission;
use App\Models\User;

it('shows a participant they won once results are published', function () {
    $participant = User::factory()->participant()->create();
    $competition = Competition::factory()->create(['results_published_at' => now()]);
    $prize = Prize::factory()->create(['competition_id' => $competition->id]);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'participant_id' => $participant->id,
        'prize_id' => $prize->id,
    ]);
    Evaluation::factory()->create(['submission_id' => $submission->id, 'score' => 95]);

    $response = $this->actingAs($participant, 'sanctum')->getJson('/api/my/submissions');

    $response->assertOk()
        ->assertJsonPath('data.0.is_winner', true)
        ->assertJsonPath('data.0.rank', 1);
});

it('does not reveal winner status before results are published', function () {
    $participant = User::factory()->participant()->create();
    $competition = Competition::factory()->create(['results_published_at' => null]);
    $prize = Prize::factory()->create(['competition_id' => $competition->id]);
    Submission::factory()->create([
        'competition_id' => $competition->id,
        'participant_id' => $participant->id,
        'prize_id' => $prize->id,
    ]);

    $response = $this->actingAs($participant, 'sanctum')->getJson('/api/my/submissions');

    $response->assertOk()
        ->assertJsonPath('data.0.is_winner', false)
        ->assertJsonPath('data.0.rank', null);
});
