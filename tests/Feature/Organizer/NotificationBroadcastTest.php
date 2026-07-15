<?php

use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\CompetitionAnnouncement;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->organizer = User::factory()->organizer()->create();
    $this->competition = Competition::factory()->create(['organizer_id' => $this->organizer->id]);
});

it('sends an announcement to all participants of the competition', function () {
    Notification::fake();

    $participantOne = User::factory()->participant()->create();
    $participantTwo = User::factory()->participant()->create();
    Submission::factory()->create(['competition_id' => $this->competition->id, 'participant_id' => $participantOne->id]);
    Submission::factory()->create(['competition_id' => $this->competition->id, 'participant_id' => $participantTwo->id]);

    $response = $this->actingAs($this->organizer)->post(
        "/organizer/competitions/{$this->competition->id}/notifications",
        ['message' => 'The deadline has been extended by one week.'],
    );

    $response->assertRedirect();

    Notification::assertSentTo([$participantOne, $participantTwo], CompetitionAnnouncement::class);
});

it('forbids sending a notification for a competition the organizer does not own', function () {
    $otherCompetition = Competition::factory()->create();

    $this->actingAs($this->organizer)
        ->post("/organizer/competitions/{$otherCompetition->id}/notifications", ['message' => 'Nope'])
        ->assertForbidden();
});
