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

        $search = $request->query('search');

        return Inertia::render('organizer/participants/index', [
            'competition' => $competition->only(['id', 'title']),
            'filters' => ['search' => $search],
            'submissions' => $competition->submissions()
                ->with('participant:id,name,email')
                ->whereHas('participant', function ($query) use ($search) {
                    $query->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    }));
                })
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
