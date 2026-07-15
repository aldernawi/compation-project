<?php

use App\Models\Competition;
use App\Models\Prize;

it('belongs to a competition', function () {
    $competition = Competition::factory()->create();

    $prize = Prize::factory()->create([
        'competition_id' => $competition->id,
        'rank' => 1,
        'winners_count' => 1,
    ]);

    expect($prize->competition->is($competition))->toBeTrue()
        ->and($competition->prizes)->toHaveCount(1)
        ->and($prize->rank)->toBe(1);
});
