<?php

namespace App\Http\Controllers\Judge;

use App\Enums\EvaluationStatus;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Judge\StoreEvaluationRequest;
use App\Models\Competition;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    public function index(Request $request, Competition $competition): Response
    {
        abort_unless($this->isAssigned($request, $competition), 403);

        $judgeId = $request->user()->id;

        return Inertia::render('judge/submissions/index', [
            'competition' => $competition->only(['id', 'title']),
            'submissions' => $competition->submissions()
                ->where('status', SubmissionStatus::Accepted)
                ->with(['participant:id,name', 'evaluations' => fn ($query) => $query->where('judge_id', $judgeId)])
                ->latest()
                ->paginate(15)
                ->through(fn (Submission $submission) => [
                    'id' => $submission->id,
                    'participant' => $submission->participant?->only(['id', 'name']),
                    'evaluation_status' => $submission->evaluations->first()?->status->value,
                ]),
        ]);
    }

    public function evaluate(Request $request, Submission $submission): Response
    {
        abort_unless($this->isAssigned($request, $submission->competition), 403);

        $existing = $submission->evaluations()->where('judge_id', $request->user()->id)->first();

        return Inertia::render('judge/submissions/evaluate', [
            'submission' => [
                'id' => $submission->id,
                'text_content' => $submission->text_content,
                'link_url' => $submission->link_url,
                'media_url' => $submission->getFirstMediaUrl('submission_files') ?: null,
                'participant' => $submission->participant?->only(['id', 'name']),
            ],
            'evaluation' => $existing?->only(['score', 'notes', 'status']),
        ]);
    }

    public function storeEvaluation(StoreEvaluationRequest $request, Submission $submission): RedirectResponse
    {
        abort_unless($this->isAssigned($request, $submission->competition), 403);

        $submission->evaluations()->updateOrCreate(
            ['judge_id' => $request->user()->id],
            [
                'score' => $request->validated('score'),
                'notes' => $request->validated('notes'),
                'status' => EvaluationStatus::Evaluated,
            ],
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Evaluation saved.')]);

        return to_route('judge.competitions.submissions.index', $submission->competition);
    }

    public function markNeedsReview(Request $request, Submission $submission): RedirectResponse
    {
        abort_unless($this->isAssigned($request, $submission->competition), 403);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $submission->evaluations()->updateOrCreate(
            ['judge_id' => $request->user()->id],
            [
                'notes' => $validated['notes'] ?? null,
                'status' => EvaluationStatus::NeedsReview,
            ],
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Marked as needs review.')]);

        return to_route('judge.competitions.submissions.index', $submission->competition);
    }

    private function isAssigned(Request $request, Competition $competition): bool
    {
        return $competition->judges()->where('judge_id', $request->user()->id)->exists();
    }
}
