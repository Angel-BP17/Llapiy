<?php

namespace App\Models\Traits;

use App\Observers\ActivityObserver;

trait LogsActivity
{
    /**
     * Boot the trait.
     */
    public static function bootLogsActivity(): void
    {
        static::observe(ActivityObserver::class);
    }
}
