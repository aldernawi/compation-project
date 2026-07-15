<?php

use App\Models\Competition;
use App\Models\Prize;
use App\Models\Submission;

it('shows published winners for a competition', function () {
    $competition = Competition::factory()->create(['results_published_at' => now()]);
    $prize = Prize::factory()->create(['competition_id' => $competition->id, 'rank' => 1]);
    $winner = Submission::factory()->create(['competition_id' => $competition->id, 'prize_id' => $prize->id]);
    Submission::factory()->create(['competition_id' => $competition->id]);

    $response = $this->getJson("/api/competitions/{$competition->id}/results");

    $response->assertOk()->assertJsonCount(1, 'data');
    expect($response->json('data.0.id'))->toBe($winner->id);
});

it('returns not found for unpublished results', function () {
    $competition = Competition::factory()->create(['results_published_at' => null]);

    $this->getJson("/api/competitions/{$competition->id}/results")->assertNotFound();
});
