<?php

use App\Enums\EvaluationStatus;
use App\Enums\SubmissionStatus;
use App\Models\Competition;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\User;

beforeEach(function () {
    $this->judge = User::factory()->judge()->create();
    $this->competition = Competition::factory()->create();
    $this->competition->judges()->attach($this->judge);
});

it('lists accepted submissions for an assigned competition', function () {
    Submission::factory()->create([
        'competition_id' => $this->competition->id,
        'status' => SubmissionStatus::Accepted,
    ]);
    Submission::factory()->create([
        'competition_id' => $this->competition->id,
        'status' => SubmissionStatus::Submitted,
    ]);

    $response = $this->actingAs($this->judge)->get("/judge/competitions/{$this->competition->id}/submissions");

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page->component('judge/submissions/index')
            ->has('submissions.data', 1)
    );
});

it('forbids listing submissions for a competition the judge is not assigned to', function () {
    $otherCompetition = Competition::factory()->create();

    $this->actingAs($this->judge)->get("/judge/competitions/{$otherCompetition->id}/submissions")->assertForbidden();
});

it('shows the evaluation form for an assigned submission', function () {
    $submission = Submission::factory()->create([
        'competition_id' => $this->competition->id,
        'status' => SubmissionStatus::Accepted,
    ]);

    $response = $this->actingAs($this->judge)->get("/judge/submissions/{$submission->id}/evaluate");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('judge/submissions/evaluate'));
});

it('forbids evaluating a submission for a competition the judge is not assigned to', function () {
    $otherCompetition = Competition::factory()->create();
    $submission = Submission::factory()->create(['competition_id' => $otherCompetition->id]);

    $this->actingAs($this->judge)->get("/judge/submissions/{$submission->id}/evaluate")->assertForbidden();
});

it('creates an evaluation with a score and notes', function () {
    $submission = Submission::factory()->create([
        'competition_id' => $this->competition->id,
        'status' => SubmissionStatus::Accepted,
    ]);

    $response = $this->actingAs($this->judge)->post("/judge/submissions/{$submission->id}/evaluate", [
        'score' => 85,
        'notes' => 'Great work',
    ]);

    $response->assertRedirect();

    $evaluation = Evaluation::where('submission_id', $submission->id)->where('judge_id', $this->judge->id)->firstOrFail();
    expect($evaluation->score)->toBe(85)
        ->and($evaluation->notes)->toBe('Great work')
        ->and($evaluation->status)->toBe(EvaluationStatus::Evaluated);
});

it('updates an existing evaluation instead of creating a duplicate', function () {
    $submission = Submission::factory()->create([
        'competition_id' => $this->competition->id,
        'status' => SubmissionStatus::Accepted,
    ]);

    Evaluation::factory()->create([
        'submission_id' => $submission->id,
        'judge_id' => $this->judge->id,
        'score' => 50,
    ]);

    $this->actingAs($this->judge)->post("/judge/submissions/{$submission->id}/evaluate", [
        'score' => 90,
        'notes' => 'Revised',
    ]);

    expect(Evaluation::where('submission_id', $submission->id)->where('judge_id', $this->judge->id)->count())->toBe(1);
    expect(Evaluation::where('submission_id', $submission->id)->first()->score)->toBe(90);
});
