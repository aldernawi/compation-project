<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\StoreCompetitionRequest;
use App\Http\Requests\Organizer\UpdateCompetitionRequest;
use App\Models\Competition;
use App\Models\CompetitionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompetitionController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('organizer/competitions/index', [
            'competitions' => $request->user()
                ->competitions()
                ->with('competitionType:id,name')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('organizer/competitions/create', [
            'competitionTypes' => CompetitionType::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreCompetitionRequest $request): RedirectResponse
    {
        $request->user()->competitions()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Competition created.')]);

        return to_route('organizer.competitions.index');
    }

    public function edit(Request $request, Competition $competition): Response
    {
        abort_unless($request->user()->can('update', $competition), 403);

        return Inertia::render('organizer/competitions/edit', [
            'competition' => $competition->only([
                'id', 'competition_type_id', 'title', 'description', 'terms',
                'starts_at', 'ends_at', 'requires_approval', 'evaluation_method',
            ]),
            'competitionTypes' => CompetitionType::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateCompetitionRequest $request, Competition $competition): RedirectResponse
    {
        $competition->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Competition updated.')]);

        return to_route('organizer.competitions.index');
    }
}
