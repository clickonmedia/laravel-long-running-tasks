<?php

namespace Clickonmedia\LongRunningTasks\Tests\TestSupport\LongRunningTasks;

use Clickonmedia\LongRunningTasks\Enums\TaskResult;
use Clickonmedia\LongRunningTasks\LongRunningTask;
use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;

class LongRunningTestTask extends LongRunningTask
{
    public function check(LongRunningTaskLogItem $logItem): TaskResult
    {
        return TaskResult::StopChecking;
    }
}
