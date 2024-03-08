<?php

namespace Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks;

use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;

class LongRunningTestTask extends LongRunningTask
{
    public function check(LongRunningTaskLogItem $logItem): TaskResult
    {
        return TaskResult::StopChecking;
    }
}
