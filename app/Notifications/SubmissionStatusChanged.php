<?php

namespace App\Notifications;

use App\Models\Submission;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SubmissionStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Submission $submission) {}

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
            'submission_id' => $this->submission->id,
            'competition_id' => $this->submission->competition_id,
            'status' => $this->submission->status->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toFcm(mixed $notifiable): array
    {
        return [
            'title' => 'Submission update',
            'body' => "Your submission is now {$this->submission->status->value}.",
        ];
    }
}
