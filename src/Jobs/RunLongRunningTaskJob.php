<?php

namespace Clickonmedia\Monitor\Jobs;

use Clickonmedia\Monitor\Enums\LogItemCheckResult;
use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;

class RunLongRunningTaskJob implements ShouldBeUnique, ShouldQueue
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

        if (! $this->longRunningTaskLogItem->shouldKeepChecking()) {
            $this->longRunningTaskLogItem->markAsCheckedEnded(LogItemStatus::DidNotComplete);

            return;
        }

        $this->dispatchAgain();
    }

    protected function dispatchAgain(): void
    {
        $this->longRunningTaskLogItem->markAsPending();

        $job = new self($this->longRunningTaskLogItem);

        $delay = $this->longRunningTaskLogItem->check_frequency_in_seconds;

        dispatch($job)->delay($delay);
    }

    public function uniqueId(): string
    {
        return $this->longRunningTaskLogItem->id;
    }
}
