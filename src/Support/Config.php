<?php

namespace Clickonmedia\Monitor\Support;

use Clickonmedia\Monitor\Exceptions\InvalidJob;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use \Clickonmedia\Monitor\Models\LongRunningTaskLogItem;

class Config
{
    /**
     * @return class-string<RunLongRunningTaskJob>
     */
    public static function getTaskJobClass(): string
    {
        $jobClass = config('long-running-tasks-monitor.task_job');

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
        $modelClass = config('long-running-tasks-monitor.log_model');

        if (! is_a($modelClass, LongRunningTaskLogItem::class, true)) {
            throw InvalidJob::make($modelClass);
        }

        return $modelClass;
    }
}
