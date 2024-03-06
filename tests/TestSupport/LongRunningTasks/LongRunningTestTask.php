<?php

namespace Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks;

use Clickonmedia\Monitor\Enums\LogItemCheckResult;
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;

class LongRunningTestTask extends LongRunningTask
{
    public function check(LongRunningTaskLogItem $logItem): LogItemCheckResult
    {
        // TODO: Implement check() method.
    }

    public function onFail(LongRunningTaskLogItem $logItem)
    {
        // TODO: Implement onFail() method.
    }
}
