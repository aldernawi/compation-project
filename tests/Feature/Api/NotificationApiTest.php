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

it('marks a notification as read', function () {
    $participant = User::factory()->participant()->create();
    $submission = Submission::factory()->create(['participant_id' => $participant->id]);

    $participant->notify(new SubmissionStatusChanged($submission));
    $notificationId = $participant->notifications()->first()->id;

    $response = $this->actingAs($participant, 'sanctum')->patchJson("/api/notifications/{$notificationId}/read");

    $response->assertNoContent();
    expect($participant->notifications()->first()->read_at)->not->toBeNull();
});

it('returns not found when marking another participant notification as read', function () {
    $participant = User::factory()->participant()->create();
    $otherParticipant = User::factory()->participant()->create();
    $submission = Submission::factory()->create(['participant_id' => $otherParticipant->id]);

    $otherParticipant->notify(new SubmissionStatusChanged($submission));
    $notificationId = $otherParticipant->notifications()->first()->id;

    $this->actingAs($participant, 'sanctum')->patchJson("/api/notifications/{$notificationId}/read")->assertNotFound();
});
