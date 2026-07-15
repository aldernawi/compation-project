<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Submission;
use App\Notifications\SubmissionStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResultsController extends Controller
{
    public function show(Competition $competition): Response
    {
        $submissions = $competition->submissions()
            ->with(['participant:id,name', 'prize:id,title,rank'])
            ->get()
            ->map(fn (Submission $submission) => [
                'id' => $submission->id,
                'participant' => $submission->participant?->only(['id', 'name']),
                'status' => $submission->status->value,
                'average_score' => $submission->averageScore(),
                'prize' => $submission->prize?->only(['id', 'title', 'rank']),
            ])
            ->sortByDesc('average_score')
            ->values();

        return Inertia::render('admin/results/show', [
            'competition' => $competition->only(['id', 'title', 'results_published_at']),
            'prizes' => $competition->prizes()->orderBy('rank')->get(['id', 'title', 'rank']),
            'submissions' => $submissions,
        ]);
    }

    public function assignPrize(Request $request, Competition $competition, Submission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'prize_id' => ['nullable', 'exists:prizes,id'],
        ]);

        $submission->update(['prize_id' => $validated['prize_id'] ?? null]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Winner updated.')]);

        return back();
    }

    public function publish(Competition $competition): RedirectResponse
    {
        $competition->update(['results_published_at' => now()]);

        $competition->submissions()
            ->whereNotNull('prize_id')
            ->with('participant')
            ->get()
            ->each(fn (Submission $submission) => $submission->participant?->notify(new SubmissionStatusChanged($submission)));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Results published.')]);

        return back();
    }
}
