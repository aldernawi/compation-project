<?php

use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;

beforeEach(function () {
    $this->organizer = User::factory()->organizer()->create();
    $this->competition = Competition::factory()->create(['organizer_id' => $this->organizer->id]);
});

it('lists submissions for the organizer own competition', function () {
    Submission::factory()->create(['competition_id' => $this->competition->id]);

    $response = $this->actingAs($this->organizer)->get("/organizer/competitions/{$this->competition->id}/submissions");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('organizer/submissions/index'));
});

it('forbids viewing submissions for a competition the organizer does not own', function () {
    $otherCompetition = Competition::factory()->create();

    $this->actingAs($this->organizer)->get("/organizer/competitions/{$otherCompetition->id}/submissions")->assertForbidden();
});

it('accepts a submission', function () {
    $submission = Submission::factory()->create([
        'competition_id' => $this->competition->id,
        'status' => SubmissionStatus::Submitted,
    ]);

    $this->actingAs($this->organizer)
        ->patch("/organizer/competitions/{$this->competition->id}/submissions/{$submission->id}/accept")
        ->assertRedirect();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Accepted);
});

it('rejects a submission with a reason', function () {
    $submission = Submission::factory()->create([
        'competition_id' => $this->competition->id,
        'status' => SubmissionStatus::Submitted,
    ]);

    $this->actingAs($this->organizer)
        ->patch("/organizer/competitions/{$this->competition->id}/submissions/{$submission->id}/reject", [
            'reason' => 'Does not meet guidelines',
        ])
        ->assertRedirect();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Rejected)
        ->and($submission->fresh()->rejection_reason)->toBe('Does not meet guidelines');
});
