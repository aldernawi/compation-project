<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $from = $request->date('from');
        $to = $request->date('to');

        $competitions = Competition::query()->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to));

        $submissions = Submission::query()->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to));

        return Inertia::render('admin/reports/index', [
            'filters' => [
                'from' => $request->query('from'),
                'to' => $request->query('to'),
            ],
            'stats' => [
                'competitions_count' => (clone $competitions)->count(),
                'participants_count' => User::query()->where('role', Role::Participant)->count(),
                'submissions_count' => (clone $submissions)->count(),
                'winners_count' => (clone $submissions)->whereNotNull('prize_id')->count(),
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
