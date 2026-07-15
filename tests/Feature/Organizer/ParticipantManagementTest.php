<?php

use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;

beforeEach(function () {
    $this->organizer = User::factory()->organizer()->create();
    $this->competition = Competition::factory()->create(['organizer_id' => $this->organizer->id]);
});

it('lists participants for the organizer own competition', function () {
    Submission::factory()->create(['competition_id' => $this->competition->id]);

    $response = $this->actingAs($this->organizer)->get("/organizer/competitions/{$this->competition->id}/participants");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('organizer/participants/index'));
});

it('forbids listing participants for a competition the organizer does not own', function () {
    $otherCompetition = Competition::factory()->create();

    $this->actingAs($this->organizer)->get("/organizer/competitions/{$otherCompetition->id}/participants")->assertForbidden();
});

it('searches participants by name', function () {
    $matching = User::factory()->participant()->create(['name' => 'Jane Doe']);
    $other = User::factory()->participant()->create(['name' => 'Someone Else']);
    Submission::factory()->create(['competition_id' => $this->competition->id, 'participant_id' => $matching->id]);
    Submission::factory()->create(['competition_id' => $this->competition->id, 'participant_id' => $other->id]);

    $response = $this->actingAs($this->organizer)->get(
        "/organizer/competitions/{$this->competition->id}/participants?search=Jane"
    );

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->has('submissions.data', 1));
});
