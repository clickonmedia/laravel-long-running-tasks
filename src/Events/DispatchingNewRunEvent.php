<?php

namespace Clickonmedia\LongRunningTasks\Events;

use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;

class DispatchingNewRunEvent
{
    public function __construct(public LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }
}
