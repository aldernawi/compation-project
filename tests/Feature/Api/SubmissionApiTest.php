<?php

use App\Enums\SubmissionKind;
use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\Submission;
use App\Models\User;

beforeEach(function () {
    $this->participant = User::factory()->participant()->create();
});

it('lets a participant submit a text entry to a text competition', function () {
    $type = CompetitionType::factory()->create(['submission_kind' => SubmissionKind::Text]);
    $competition = Competition::factory()->create(['competition_type_id' => $type->id]);

    $response = $this->actingAs($this->participant, 'sanctum')->postJson(
        "/api/competitions/{$competition->id}/submissions",
        ['text_content' => 'My entry text'],
    );

    $response->assertCreated();

    $submission = Submission::where('competition_id', $competition->id)->firstOrFail();
    expect($submission->participant_id)->toBe($this->participant->id)
        ->and($submission->text_content)->toBe('My entry text')
        ->and($submission->status)->toBe(SubmissionStatus::Submitted);
});

it('lets a participant submit a link entry to a link competition', function () {
    $type = CompetitionType::factory()->create(['submission_kind' => SubmissionKind::Link]);
    $competition = Competition::factory()->create(['competition_type_id' => $type->id]);

    $response = $this->actingAs($this->participant, 'sanctum')->postJson(
        "/api/competitions/{$competition->id}/submissions",
        ['link_url' => 'https://example.com/my-entry'],
    );

    $response->assertCreated();
    expect(Submission::where('competition_id', $competition->id)->firstOrFail()->link_url)->toBe('https://example.com/my-entry');
});

it('rejects a text submission missing text_content', function () {
    $type = CompetitionType::factory()->create(['submission_kind' => SubmissionKind::Text]);
    $competition = Competition::factory()->create(['competition_type_id' => $type->id]);

    $this->actingAs($this->participant, 'sanctum')
        ->postJson("/api/competitions/{$competition->id}/submissions", [])
        ->assertUnprocessable();
});

it('lists the authenticated participant own submissions', function () {
    Submission::factory()->create(['participant_id' => $this->participant->id]);
    Submission::factory()->create();

    $response = $this->actingAs($this->participant, 'sanctum')->getJson('/api/my/submissions');

    $response->assertOk()->assertJsonCount(1, 'data');
});

it('lets a participant edit their own submitted entry before acceptance', function () {
    $type = CompetitionType::factory()->create(['submission_kind' => SubmissionKind::Text]);
    $competition = Competition::factory()->create(['competition_type_id' => $type->id]);

    $submission = Submission::factory()->create([
        'competition_id' => $competition->id,
        'participant_id' => $this->participant->id,
        'status' => SubmissionStatus::Submitted,
        'text_content' => 'Old text',
    ]);

    $response = $this->actingAs($this->participant, 'sanctum')->putJson("/api/submissions/{$submission->id}", [
        'text_content' => 'Updated text',
    ]);

    $response->assertOk();
    expect($submission->fresh()->text_content)->toBe('Updated text');
});

it('forbids editing a submission that has already been accepted', function () {
    $submission = Submission::factory()->create([
        'participant_id' => $this->participant->id,
        'status' => SubmissionStatus::Accepted,
    ]);

    $this->actingAs($this->participant, 'sanctum')
        ->putJson("/api/submissions/{$submission->id}", ['text_content' => 'Nope'])
        ->assertForbidden();
});

it('forbids editing another participant submission', function () {
    $submission = Submission::factory()->create(['status' => SubmissionStatus::Submitted]);

    $this->actingAs($this->participant, 'sanctum')
        ->putJson("/api/submissions/{$submission->id}", ['text_content' => 'Nope'])
        ->assertForbidden();
});
