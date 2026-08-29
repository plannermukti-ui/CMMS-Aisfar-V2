<?php

namespace App\Observers;

use App\Models\ActivityLog;

class ActivityObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created($model): void
    {
        ActivityLog::log('create', class_basename($model), $model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated($model): void
    {
        ActivityLog::log('update', class_basename($model), $model, $model->getOriginal());
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted($model): void
    {
        ActivityLog::log('delete', class_basename($model), $model, $model->toArray());
    }
}
