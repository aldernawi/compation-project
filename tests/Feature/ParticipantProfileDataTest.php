<?php

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;

it('stores an age when a participant registers through the API', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone_number' => '+15551234567',
        'age' => 25,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated()->assertJsonPath('user.age', 25);

    expect(User::where('email', 'jane@example.com')->value('age'))->toBe(25);
});

it('updates a participant age through the profile API', function () {
    $participant = User::factory()->participant()->create(['age' => 20]);

    $response = $this->actingAs($participant, 'sanctum')->putJson('/api/profile', [
        'name' => $participant->name,
        'email' => $participant->email,
        'phone_number' => '+15551234567',
        'age' => 21,
    ]);

    $response->assertOk()->assertJsonPath('data.age', 21);
    expect($participant->fresh()->age)->toBe(21);
});

it('shows participant contact data to the organizer of their competition', function () {
    $organizer = User::factory()->organizer()->create();
    $participant = User::factory()->participant()->create([
        'age' => 25,
        'phone_number' => '+15551234567',
    ]);
    $competition = Competition::factory()->create(['organizer_id' => $organizer->id]);
    Submission::factory()->create([
        'competition_id' => $competition->id,
        'participant_id' => $participant->id,
    ]);

    $response = $this->actingAs($organizer)->get("/organizer/competitions/{$competition->id}/participants");

    $response->assertOk()->assertInertia(fn ($page) => $page
        ->where('submissions.data.0.participant.age', 25)
        ->where('submissions.data.0.participant.phone_number', '+15551234567')
    );
});

it('shows participant contact data to an assigned judge', function () {
    $judge = User::factory()->judge()->create();
    $participant = User::factory()->participant()->create([
        'age' => 25,
        'phone_number' => '+15551234567',
    ]);
    $competition = Competition::factory()->create();
    $competition->judges()->attach($judge);
    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'participant_id' => $participant->id,
        'status' => SubmissionStatus::Accepted,
    ]);

    $response = $this->actingAs($judge)->get("/judge/submissions/{$submission->id}/evaluate");

    $response->assertOk()->assertInertia(fn ($page) => $page
        ->where('submission.participant.age', 25)
        ->where('submission.participant.phone_number', '+15551234567')
    );
});
