<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompetitionController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('judge/competitions/index', [
            'competitions' => $request->user()
                ->judgedCompetitions()
                ->with('competitionType:id,name')
                ->latest('competitions.created_at')
                ->paginate(15),
        ]);
    }
}
