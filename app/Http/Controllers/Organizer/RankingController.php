<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
{
    public function index(Request $request, Competition $competition): Response
    {
        abort_unless($request->user()->can('update', $competition), 403);

        $submissions = $competition->submissions()
            ->with(['participant:id,name', 'evaluations.judge:id,name'])
            ->get()
            ->map(fn (Submission $submission) => [
                'id' => $submission->id,
                'participant' => $submission->participant?->only(['id', 'name']),
                'average_score' => $submission->averageScore(),
                'evaluations' => $submission->evaluations->map(fn ($evaluation) => [
                    'judge' => $evaluation->judge?->only(['id', 'name']),
                    'score' => $evaluation->score,
                    'notes' => $evaluation->notes,
                ])->values()->all(),
            ])
            ->sortByDesc('average_score')
            ->values();

        return Inertia::render('organizer/rankings/index', [
            'competition' => $competition->only(['id', 'title']),
            'submissions' => $submissions,
        ]);
    }
}
