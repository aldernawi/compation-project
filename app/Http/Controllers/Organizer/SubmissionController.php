<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
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
        abort_unless($request->user()->can('update', $competition), 403);

        $status = $request->query('status');

        return Inertia::render('organizer/submissions/index', [
            'competition' => $competition->only(['id', 'title']),
            'filters' => ['status' => $status],
            'submissions' => $competition->submissions()
                ->with('participant:id,name')
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(15),
        ]);
    }

    public function accept(Request $request, Competition $competition, Submission $submission): RedirectResponse
    {
        abort_unless($request->user()->can('update', $competition), 403);

        $submission->update(['status' => SubmissionStatus::Accepted, 'rejection_reason' => null]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Submission accepted.')]);

        return to_route('organizer.competitions.submissions.index', $competition);
    }

    public function reject(Request $request, Competition $competition, Submission $submission): RedirectResponse
    {
        abort_unless($request->user()->can('update', $competition), 403);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $submission->update(['status' => SubmissionStatus::Rejected, 'rejection_reason' => $validated['reason']]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Submission rejected.')]);

        return to_route('organizer.competitions.submissions.index', $competition);
    }
}
