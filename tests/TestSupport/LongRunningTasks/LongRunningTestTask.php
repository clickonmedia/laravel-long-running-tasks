<?php

namespace Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks;

use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Closure;
use Exception;

class LongRunningTestTask extends LongRunningTask
{

    public function check(LongRunningTaskLogItem $logItem): TaskResult
    {
        return TaskResult::StopChecking;
    }
}
