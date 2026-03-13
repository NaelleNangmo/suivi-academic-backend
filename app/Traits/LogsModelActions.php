<?php

namespace App\Traits;

use App\Services\ActionLogger;

trait LogsModelActions
{
    protected static function bootLogsModelActions()
    {
        static::created(function ($model) {
            ActionLogger::logCreate(class_basename($model), $model);
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            if (!empty($changes)) {
                ActionLogger::logUpdate(class_basename($model), $model, $changes);
            }
        });

        static::deleted(function ($model) {
            ActionLogger::logDelete(class_basename($model), $model);
        });
    }
}