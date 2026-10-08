<?php

namespace App\Observers;

use App\Models\Notification;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;

class NotificationObserver
{
    /**
     * Push each new in-app notification to the user's browser subscriptions.
     * Best-effort: failures are logged and never block the originating request.
     */
    public function created(Notification $notification): void
    {
        $service = WebPushService::fromConfig();
        if (! $service->isConfigured() || ! $notification->user_id) {
            return;
        }

        $payload = [
            'title' => $notification->title,
            'body' => $notification->message,
            'url' => '/profile/notifications',
            'tag' => 'notification-' . $notification->id,
        ];

        foreach ($notification->user->pushSubscriptions as $subscription) {
            try {
                $status = $service->send($subscription, $payload);
                // 404/410 = subscription no longer valid; prune it.
                if ($status === 404 || $status === 410) {
                    $subscription->delete();
                }
            } catch (\Throwable $e) {
                Log::warning('WebPush send failed', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
