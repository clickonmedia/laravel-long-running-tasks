<?php

namespace Clickonmedia\LongRunningTasks\Support;

use Clickonmedia\LongRunningTasks\Exceptions\InvalidJob;
use Clickonmedia\LongRunningTasks\Exceptions\InvalidModel;
use Clickonmedia\LongRunningTasks\Jobs\RunLongRunningTaskJob;
use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;

class Config
{
    /**
     * @return class-string<RunLongRunningTaskJob>
     */
    public static function getTaskJobClass(): string
    {
        $jobClass = config('long-running-tasks.task_job');

        if (! is_a($jobClass, RunLongRunningTaskJob::class, true)) {
            throw InvalidJob::make($jobClass);
        }

        return $jobClass;
    }

    /**
     * @return class-string<LongRunningTaskLogItem>
     */
    public static function getLongRunningTaskLogItemModelClass(): string
    {
        $modelClass = config('long-running-tasks.log_model');

        if (! is_a($modelClass, LongRunningTaskLogItem::class, true)) {
            throw InvalidModel::make($modelClass);
        }

        return $modelClass;
    }
}
