<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\StoreNotificationRequest;
use App\Models\Competition;
use App\Models\User;
use App\Notifications\CompetitionAnnouncement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function create(Request $request, Competition $competition): Response
    {
        abort_unless($request->user()->can('update', $competition), 403);

        return Inertia::render('organizer/notifications/create', [
            'competition' => $competition->only(['id', 'title']),
        ]);
    }

    public function store(StoreNotificationRequest $request, Competition $competition): RedirectResponse
    {
        $participants = User::query()
            ->whereIn('id', $competition->submissions()->select('participant_id'))
            ->get();

        NotificationFacade::send($participants, new CompetitionAnnouncement($competition, $request->validated('message')));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Notification sent.')]);

        return back();
    }
}
