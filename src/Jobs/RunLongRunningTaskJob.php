<?php

namespace Clickonmedia\Monitor\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Clickonmedia\Monitor\Enums\LogItemCheckResult;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;

class RunLongRunningTaskJob implements ShouldQueue
{
    public function __construct(protected LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }

    public function handle()
    {
        $task = $this->longRunningTaskLogItem->task();

        try {
            $checkResult = $task->check($this->longRunningTaskLogItem);
        } catch (Exception $exception) {
            // TODO: handle failure
        }

        if ($checkResult === LogItemCheckResult::StopChecking) {
            return;
        }

        $job = new self($this->longRunningTaskLogItem);
        $delay = $this->longRunningTaskLogItem->check_frequency_in_seconds;

        dispatch($job)->delay($delay);
    }
}