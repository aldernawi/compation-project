<?php

use App\Models\Competition;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\User;

beforeEach(function () {
    $this->organizer = User::factory()->organizer()->create();
    $this->competition = Competition::factory()->create(['organizer_id' => $this->organizer->id]);
});

it('shows rankings for the organizer own competition', function () {
    $submission = Submission::factory()->create(['competition_id' => $this->competition->id]);
    Evaluation::factory()->create(['submission_id' => $submission->id, 'score' => 90]);

    $response = $this->actingAs($this->organizer)->get("/organizer/competitions/{$this->competition->id}/rankings");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('organizer/rankings/index'));
});

it('forbids viewing rankings for a competition the organizer does not own', function () {
    $otherCompetition = Competition::factory()->create();

    $this->actingAs($this->organizer)->get("/organizer/competitions/{$otherCompetition->id}/rankings")->assertForbidden();
});
