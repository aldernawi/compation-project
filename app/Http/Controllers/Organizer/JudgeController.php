<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class JudgeController extends Controller
{
    public function index(Request $request, Competition $competition): Response
    {
        abort_unless($request->user()->can('update', $competition), 403);

        return Inertia::render('organizer/judges/index', [
            'competition' => $competition->only(['id', 'title']),
            'judges' => $competition->judges()->get(['users.id', 'users.name', 'users.email']),
            'availableJudges' => User::query()
                ->where('role', Role::Judge)
                ->whereNotIn('id', $competition->judges()->pluck('users.id'))
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, Competition $competition): RedirectResponse
    {
        abort_unless($request->user()->can('update', $competition), 403);

        $validated = $request->validate([
            'judge_id' => ['required', Rule::exists('users', 'id')->where('role', Role::Judge->value)],
        ]);

        $competition->judges()->syncWithoutDetaching([$validated['judge_id']]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Judge assigned.')]);

        return to_route('organizer.competitions.judges.index', $competition);
    }

    public function destroy(Request $request, Competition $competition, User $judge): RedirectResponse
    {
        abort_unless($request->user()->can('update', $competition), 403);

        $competition->judges()->detach($judge->id);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Judge removed.')]);

        return to_route('organizer.competitions.judges.index', $competition);
    }
}
