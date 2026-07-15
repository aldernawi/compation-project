<?php

namespace App\Notifications;

use App\Models\Competition;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CompetitionAnnouncement extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Competition $competition, public string $message) {}

    /**
     * @return array<int, string|class-string>
     */
    public function via(mixed $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'competition_id' => $this->competition->id,
            'message' => $this->message,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toFcm(mixed $notifiable): array
    {
        return [
            'title' => $this->competition->title,
            'body' => $this->message,
        ];
    }
}
