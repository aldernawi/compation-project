<?php

use App\Models\Submission;
use App\Models\User;
use App\Notifications\SubmissionStatusChanged;

it('lists the authenticated participant own notifications', function () {
    $participant = User::factory()->participant()->create();
    $submission = Submission::factory()->create(['participant_id' => $participant->id]);

    $participant->notify(new SubmissionStatusChanged($submission));

    $response = $this->actingAs($participant, 'sanctum')->getJson('/api/notifications');

    $response->assertOk()->assertJsonCount(1, 'data');
});

it('requires authentication to list notifications', function () {
    $this->getJson('/api/notifications')->assertUnauthorized();
});
