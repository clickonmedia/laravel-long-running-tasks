<?php

namespace Clickonmedia\LongRunningTasks\Events;

use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;

class TaskRunStartingEvent
{
    public function __construct(public LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }
}
