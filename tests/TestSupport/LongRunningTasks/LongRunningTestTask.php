<?php

namespace Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks;

use Clickonmedia\Monitor\Enums\LogItemCheckResult;
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Closure;
use Exception;

class LongRunningTestTask extends LongRunningTask
{
    public function __construct(protected ?Closure $callable = null)
    {

    }

    public function check(LongRunningTaskLogItem $logItem): LogItemCheckResult
    {
        if ($this->callable) {
            return ($this->callable)($logItem);
        }

        return LogItemCheckResult::StopChecking;
    }

    public function onFail(LongRunningTaskLogItem $logItem, Exception $exception): LogItemCheckResult
    {
        return LogItemCheckResult::StopChecking;
    }
}
