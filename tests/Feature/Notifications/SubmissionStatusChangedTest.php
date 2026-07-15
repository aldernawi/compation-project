<?php

use App\Models\Submission;
use App\Notifications\SubmissionStatusChanged;
use Illuminate\Support\Facades\Notification;

it('sends a database notification when a submission status changes', function () {
    Notification::fake();

    $submission = Submission::factory()->create();

    $submission->participant->notify(new SubmissionStatusChanged($submission));

    Notification::assertSentTo(
        $submission->participant,
        SubmissionStatusChanged::class,
        fn (SubmissionStatusChanged $notification) => $notification->toArray($submission->participant)['submission_id'] === $submission->id
    );
});

it('stores the notification in the database channel', function () {
    $submission = Submission::factory()->create();

    $submission->participant->notify(new SubmissionStatusChanged($submission));

    expect($submission->participant->notifications()->count())->toBe(1);
    expect($submission->participant->notifications()->first()->data['status'])->toBe($submission->status->value);
});
