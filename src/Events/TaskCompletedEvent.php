<?php

namespace Clickonmedia\LongRunningTasks\Events;

use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;

class TaskCompletedEvent
{
    public function __construct(public LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }
}
