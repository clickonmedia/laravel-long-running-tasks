<?php

namespace Clickonmedia\Monitor\Support;

use Clickonmedia\Monitor\Exceptions\InvalidJob;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;

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
}
