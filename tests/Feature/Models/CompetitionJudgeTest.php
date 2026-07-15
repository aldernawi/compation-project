<?php

use App\Models\Competition;
use App\Models\User;

it('assigns a judge to a competition', function () {
    $competition = Competition::factory()->create();
    $judge = User::factory()->judge()->create();

    $competition->judges()->attach($judge);

    expect($competition->judges)->toHaveCount(1)
        ->and($competition->judges->first()->is($judge))->toBeTrue()
        ->and($judge->judgedCompetitions)->toHaveCount(1);
});
