<?php

use App\Models\Competition;
use App\Models\Prize;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\SubmissionStatusChanged;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('shows results for a competition', function () {
    $competition = Competition::factory()->create();
    Submission::factory()->create(['competition_id' => $competition->id]);

    $response = $this->actingAs($this->admin)->get("/admin/competitions/{$competition->id}/results");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/results/show'));
});

it('forbids a non-admin from viewing results', function () {
    $competition = Competition::factory()->create();
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get("/admin/competitions/{$competition->id}/results")->assertForbidden();
});

it('assigns a prize to a submission', function () {
    $competition = Competition::factory()->create();
    $prize = Prize::factory()->create(['competition_id' => $competition->id]);
    $submission = Submission::factory()->create(['competition_id' => $competition->id]);

    $response = $this->actingAs($this->admin)->patch(
        "/admin/competitions/{$competition->id}/results/submissions/{$submission->id}",
        ['prize_id' => $prize->id],
    );

    $response->assertRedirect();
    expect($submission->fresh()->prize_id)->toBe($prize->id);
});

it('publishes results and notifies winners', function () {
    Notification::fake();

    $competition = Competition::factory()->create();
    $prize = Prize::factory()->create(['competition_id' => $competition->id]);
    $submission = Submission::factory()->create(['competition_id' => $competition->id, 'prize_id' => $prize->id]);

    $response = $this->actingAs($this->admin)->post("/admin/competitions/{$competition->id}/results/publish");

    $response->assertRedirect();
    expect($competition->fresh()->results_published_at)->not->toBeNull();

    Notification::assertSentTo($submission->participant, SubmissionStatusChanged::class);
});
