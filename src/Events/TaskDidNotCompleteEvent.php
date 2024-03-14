<?php

namespace Clickonmedia\LongRunningTasks\Events;

use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;

class TaskDidNotCompleteEvent
{
    public function __construct(public LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }
}
