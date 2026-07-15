<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePrizeRequest;
use App\Http\Requests\Admin\UpdatePrizeRequest;
use App\Models\Competition;
use App\Models\Prize;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PrizeController extends Controller
{
    public function index(Competition $competition): Response
    {
        return Inertia::render('admin/prizes/index', [
            'competition' => $competition->only(['id', 'title']),
            'prizes' => $competition->prizes()->orderBy('rank')->paginate(15),
        ]);
    }

    public function create(Competition $competition): Response
    {
        return Inertia::render('admin/prizes/create', [
            'competition' => $competition->only(['id', 'title']),
        ]);
    }

    public function store(StorePrizeRequest $request, Competition $competition): RedirectResponse
    {
        $competition->prizes()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Prize created.')]);

        return to_route('admin.competitions.prizes.index', $competition);
    }

    public function edit(Competition $competition, Prize $prize): Response
    {
        return Inertia::render('admin/prizes/edit', [
            'competition' => $competition->only(['id', 'title']),
            'prize' => $prize->only(['id', 'title', 'description', 'winners_count', 'rank']),
        ]);
    }

    public function update(UpdatePrizeRequest $request, Competition $competition, Prize $prize): RedirectResponse
    {
        $prize->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Prize updated.')]);

        return to_route('admin.competitions.prizes.index', $competition);
    }

    public function destroy(Competition $competition, Prize $prize): RedirectResponse
    {
        $prize->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Prize deleted.')]);

        return to_route('admin.competitions.prizes.index', $competition);
    }
}
