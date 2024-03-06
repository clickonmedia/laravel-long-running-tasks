<?php

namespace Clickonmedia\Monitor\Jobs;

use Clickonmedia\Monitor\Enums\LogItemCheckResult;
use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

class RunLongRunningTaskJob implements ShouldQueue
{
    public function __construct(protected LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }

    public function handle()
    {
        $task = $this->longRunningTaskLogItem->task();

        try {
            $this->longRunningTaskLogItem->markAsRunning();

            $checkResult = $task->check($this->longRunningTaskLogItem);
        } catch (Exception $exception) {
            $checkResult = $task->onFail($this->longRunningTaskLogItem, $exception);

            $checkResult ??= LogItemCheckResult::StopChecking;

            $this->longRunningTaskLogItem->update([
                'exception' => [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ],
            ]);
        }

        if ($checkResult === LogItemCheckResult::StopChecking) {
            $this->longRunningTaskLogItem->markAsCheckedEnded(LogItemStatus::Completed);

            return;
        }

        $this->longRunningTaskLogItem->markAsPending();

        $job = new self($this->longRunningTaskLogItem);

        $delay = $this->longRunningTaskLogItem->check_frequency_in_seconds;

        dispatch($job)->delay($delay);
    }
}
