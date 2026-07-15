<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class FcmChannel
{
    /**
     * Send the notification via FCM.
     *
     * Wiring to an actual FCM-sending package (e.g. kreait/laravel-firebase)
     * happens once Firebase credentials are supplied; until then this is a
     * no-op if the user has no fcm_token.
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        $token = $notifiable->fcm_token ?? null;

        if (! $token || ! method_exists($notification, 'toFcm')) {
            return;
        }

        // Actual push send is implemented when Firebase credentials are configured.
    }
}
