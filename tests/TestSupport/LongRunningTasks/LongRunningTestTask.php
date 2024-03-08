<?php

namespace Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks;

use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Closure;
use Exception;

class LongRunningTestTask extends LongRunningTask
{
    public static ?Closure $checkClosure = null;

    public function check(LongRunningTaskLogItem $logItem): TaskResult
    {
        if (self::$checkClosure) {
            return (self::$checkClosure)($logItem);
        }

        return TaskResult::StopChecking;
    }

    public function onFail(LongRunningTaskLogItem $logItem, Exception $exception): TaskResult
    {
        return TaskResult::StopChecking;
    }
}
