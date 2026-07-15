<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/submissions/index', [
            'submissions' => Submission::query()
                ->with(['competition:id,title', 'participant:id,name'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function accept(Submission $submission): RedirectResponse
    {
        $submission->update(['status' => SubmissionStatus::Accepted]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Submission accepted.')]);

        return to_route('admin.submissions.index');
    }

    public function reject(Submission $submission): RedirectResponse
    {
        $submission->update(['status' => SubmissionStatus::Rejected]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Submission rejected.')]);

        return to_route('admin.submissions.index');
    }

    public function destroy(Submission $submission): RedirectResponse
    {
        $submission->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Submission deleted.')]);

        return to_route('admin.submissions.index');
    }
}
