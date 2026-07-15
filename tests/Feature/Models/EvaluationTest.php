<?php

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\QueryException;

it('records one evaluation per judge per submission and averages the score', function () {
    $submission = Submission::factory()->create();
    $judgeOne = User::factory()->judge()->create();
    $judgeTwo = User::factory()->judge()->create();

    Evaluation::factory()->create([
        'submission_id' => $submission->id,
        'judge_id' => $judgeOne->id,
        'score' => 80,
        'status' => EvaluationStatus::Evaluated,
    ]);

    Evaluation::factory()->create([
        'submission_id' => $submission->id,
        'judge_id' => $judgeTwo->id,
        'score' => 90,
        'status' => EvaluationStatus::Evaluated,
    ]);

    expect($submission->evaluations)->toHaveCount(2)
        ->and($submission->averageScore())->toBe(85.0);
});

it('rejects a second evaluation from the same judge for the same submission', function () {
    $submission = Submission::factory()->create();
    $judge = User::factory()->judge()->create();

    Evaluation::factory()->create(['submission_id' => $submission->id, 'judge_id' => $judge->id]);

    Evaluation::factory()->create(['submission_id' => $submission->id, 'judge_id' => $judge->id]);
})->throws(QueryException::class);
