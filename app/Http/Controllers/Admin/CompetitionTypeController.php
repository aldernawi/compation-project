<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompetitionTypeRequest;
use App\Http\Requests\Admin\UpdateCompetitionTypeRequest;
use App\Models\CompetitionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompetitionTypeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/competition-types/index', [
            'competitionTypes' => CompetitionType::query()->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/competition-types/create');
    }

    public function store(StoreCompetitionTypeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        CompetitionType::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Competition type created.')]);

        return to_route('admin.competition-types.index');
    }

    public function edit(CompetitionType $competitionType): Response
    {
        return Inertia::render('admin/competition-types/edit', [
            'competitionType' => $competitionType->only(['id', 'name', 'description', 'submission_kind']),
        ]);
    }

    public function update(UpdateCompetitionTypeRequest $request, CompetitionType $competitionType): RedirectResponse
    {
        $validated = $request->validated();

        $competitionType->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Competition type updated.')]);

        return to_route('admin.competition-types.index');
    }

    public function destroy(CompetitionType $competitionType): RedirectResponse
    {
        $competitionType->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Competition type deleted.')]);

        return to_route('admin.competition-types.index');
    }
}
