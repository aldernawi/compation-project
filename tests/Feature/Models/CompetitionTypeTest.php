<?php

use App\Enums\SubmissionKind;
use App\Models\CompetitionType;

it('creates a competition type with a submission kind', function () {
    $type = CompetitionType::factory()->create([
        'name' => 'Photography',
        'submission_kind' => SubmissionKind::Image,
    ]);

    expect($type->name)->toBe('Photography')
        ->and($type->slug)->not->toBeEmpty()
        ->and($type->submission_kind)->toBe(SubmissionKind::Image);
});
