<?php

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;

it('belongs to a competition and a participant, and accepts text content', function () {
    $competition = Competition::factory()->create();
    $participant = User::factory()->participant()->create();

    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'participant_id' => $participant->id,
        'text_content' => 'My entry text',
        'status' => SubmissionStatus::Submitted,
    ]);

    expect($submission->competition->is($competition))->toBeTrue()
        ->and($submission->participant->is($participant))->toBeTrue()
        ->and($submission->text_content)->toBe('My entry text')
        ->and($submission->status)->toBe(SubmissionStatus::Submitted)
        ->and($competition->submissions)->toHaveCount(1)
        ->and($participant->submissions)->toHaveCount(1);
});

it('can attach a media file to a submission', function () {
    $submission = Submission::factory()->create();

    $submission->addMediaFromString('fake file contents')
        ->usingFileName('entry.jpg')
        ->toMediaCollection('submission_files');

    expect($submission->getMedia('submission_files'))->toHaveCount(1);
});
