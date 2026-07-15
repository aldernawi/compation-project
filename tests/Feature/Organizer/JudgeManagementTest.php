<?php

use App\Models\Competition;
use App\Models\User;

beforeEach(function () {
    $this->organizer = User::factory()->organizer()->create();
    $this->competition = Competition::factory()->create(['organizer_id' => $this->organizer->id]);
});

it('lists judges for the organizer own competition', function () {
    $judge = User::factory()->judge()->create();
    $this->competition->judges()->attach($judge);

    $response = $this->actingAs($this->organizer)->get("/organizer/competitions/{$this->competition->id}/judges");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('organizer/judges/index'));
});

it('forbids listing judges for a competition the organizer does not own', function () {
    $otherCompetition = Competition::factory()->create();

    $this->actingAs($this->organizer)->get("/organizer/competitions/{$otherCompetition->id}/judges")->assertForbidden();
});

it('assigns a judge to the competition', function () {
    $judge = User::factory()->judge()->create();

    $response = $this->actingAs($this->organizer)->post("/organizer/competitions/{$this->competition->id}/judges", [
        'judge_id' => $judge->id,
    ]);

    $response->assertRedirect();
    expect($this->competition->judges()->where('judge_id', $judge->id)->exists())->toBeTrue();
});

it('removes a judge from the competition', function () {
    $judge = User::factory()->judge()->create();
    $this->competition->judges()->attach($judge);

    $response = $this->actingAs($this->organizer)->delete("/organizer/competitions/{$this->competition->id}/judges/{$judge->id}");

    $response->assertRedirect();
    expect($this->competition->judges()->where('judge_id', $judge->id)->exists())->toBeFalse();
});
