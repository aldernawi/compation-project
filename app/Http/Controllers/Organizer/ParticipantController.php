<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ParticipantController extends Controller
{
    public function index(Request $request, Competition $competition): Response
    {
        abort_unless($request->user()->can('update', $competition), 403);

        return Inertia::render('organizer/participants/index', [
            'competition' => $competition->only(['id', 'title']),
            'submissions' => $competition->submissions()
                ->with('participant:id,name,email')
                ->latest()
                ->paginate(15, ['id', 'participant_id', 'status'])
                ->through(fn (Submission $submission) => [
                    'id' => $submission->id,
                    'status' => $submission->status->value,
                    'participant' => $submission->participant?->only(['id', 'name', 'email']),
                ]),
        ]);
    }
}
