<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class NotificationCacheObserver
{
    /**
     * Clear notification cache when model is created
     */
    public function created($model): void
    {
        $this->clearNotificationCache();
    }

    /**
     * Clear notification cache when model is updated
     */
    public function updated($model): void
    {
        $this->clearNotificationCache();
    }

    /**
     * Clear notification cache when model is deleted
     */
    public function deleted($model): void
    {
        $this->clearNotificationCache();
    }

    /**
     * Clear the notification cache
     */
    protected function clearNotificationCache(): void
    {
        Cache::forget('topbar_notifications');
    }
}
