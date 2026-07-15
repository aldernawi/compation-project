<?php

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\Prize;

it('lists competitions without requiring authentication', function () {
    Competition::factory()->count(3)->create(['status' => CompetitionStatus::Open]);

    $response = $this->getJson('/api/competitions');

    $response->assertOk()->assertJsonCount(3, 'data');
});

it('shows a single competition with its prizes and type', function () {
    $competition = Competition::factory()->create();
    Prize::factory()->create(['competition_id' => $competition->id, 'rank' => 1]);

    $response = $this->getJson("/api/competitions/{$competition->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $competition->id)
        ->assertJsonCount(1, 'data.prizes');
});
