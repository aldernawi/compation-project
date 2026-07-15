<?php

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\User;

it('belongs to an organizer and a competition type', function () {
    $organizer = User::factory()->organizer()->create();

    $competition = Competition::factory()->create([
        'organizer_id' => $organizer->id,
        'status' => CompetitionStatus::Open,
    ]);

    expect($competition->organizer->is($organizer))->toBeTrue()
        ->and($competition->competitionType)->not->toBeNull()
        ->and($competition->status)->toBe(CompetitionStatus::Open)
        ->and($organizer->competitions)->toHaveCount(1);
});
