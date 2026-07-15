<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompetitionRequest;
use App\Http\Requests\Admin\UpdateCompetitionRequest;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CompetitionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/competitions/index', [
            'competitions' => Competition::query()
                ->with(['organizer:id,name', 'competitionType:id,name'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/competitions/create', [
            'organizers' => User::query()->where('role', Role::Organizer)->orderBy('name')->get(['id', 'name']),
            'competitionTypes' => CompetitionType::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreCompetitionRequest $request): RedirectResponse
    {
        Competition::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Competition created.')]);

        return to_route('admin.competitions.index');
    }

    public function edit(Competition $competition): Response
    {
        return Inertia::render('admin/competitions/edit', [
            'competition' => $competition->only([
                'id', 'organizer_id', 'competition_type_id', 'title', 'description', 'terms',
                'starts_at', 'ends_at', 'status', 'requires_approval', 'evaluation_method',
            ]),
            'organizers' => User::query()->where('role', Role::Organizer)->orderBy('name')->get(['id', 'name']),
            'competitionTypes' => CompetitionType::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateCompetitionRequest $request, Competition $competition): RedirectResponse
    {
        $competition->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Competition updated.')]);

        return to_route('admin.competitions.index');
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Competition deleted.')]);

        return to_route('admin.competitions.index');
    }
}
