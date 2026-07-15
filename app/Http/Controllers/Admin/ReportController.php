<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/reports/index', [
            'stats' => [
                'competitions_count' => Competition::query()->count(),
                'participants_count' => User::query()->where('role', Role::Participant)->count(),
                'submissions_count' => Submission::query()->count(),
                'winners_count' => Submission::query()->whereNotNull('prize_id')->count(),
            ],
            'mostParticipatedCompetitions' => Competition::query()
                ->withCount('submissions')
                ->orderByDesc('submissions_count')
                ->limit(5)
                ->get(['id', 'title'])
                ->map(fn (Competition $competition) => [
                    'id' => $competition->id,
                    'title' => $competition->title,
                    'submissions_count' => $competition->submissions_count,
                ]),
            'submissionsByType' => DB::table('submissions')
                ->join('competitions', 'competitions.id', '=', 'submissions.competition_id')
                ->join('competition_types', 'competition_types.id', '=', 'competitions.competition_type_id')
                ->selectRaw('competition_types.name as type, count(*) as count')
                ->groupBy('competition_types.name')
                ->get(),
        ]);
    }
}
