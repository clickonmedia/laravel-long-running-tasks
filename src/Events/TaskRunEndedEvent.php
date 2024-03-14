<?php

namespace Clickonmedia\LongRunningTasks\Events;

use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;

class TaskRunEndedEvent
{
    public function __construct(public LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }
}
