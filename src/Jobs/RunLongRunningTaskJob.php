<?php

namespace Clickonmedia\Monitor\Jobs;

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunLongRunningTaskJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }

    public function handle()
    {
        $task = $this->longRunningTaskLogItem->task();

        $this->longRunningTaskLogItem->markAsRunning();

        try {
            $checkResult = $task->check($this->longRunningTaskLogItem);
        } catch (Exception $exception) {
            $this->handleException($task, $exception);

            return;
        }

        $this->handleTaskResult($checkResult);
    }

    protected function handleTaskResult(TaskResult $checkResult): void
    {
        if ($checkResult === TaskResult::StopChecking) {
            $this->longRunningTaskLogItem->markAsCheckedEnded(LogItemStatus::Completed);

            return;
        }

        if (! $this->longRunningTaskLogItem->shouldKeepChecking()) {
            $this->longRunningTaskLogItem->markAsCheckedEnded(LogItemStatus::DidNotComplete);

            return;
        }

        $this->dispatchAgain();
    }

    protected function handleException(LongRunningTask $task, Exception $exception): void
    {
        $checkResult = $task->onFail($this->longRunningTaskLogItem, $exception);

        $checkResult ??= TaskResult::StopChecking;

        $this->longRunningTaskLogItem->update([
            'latest_exception' => [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);

        $this->longRunningTaskLogItem->markAsCheckedEnded(LogItemStatus::Failed);

        if ($checkResult == TaskResult::ContinueChecking) {
            $this->dispatchAgain();
        }
    }

    protected function dispatchAgain(): void
    {
        $this->longRunningTaskLogItem->markAsPending();

        $job = new self($this->longRunningTaskLogItem);

        $delay = $this->longRunningTaskLogItem->check_frequency_in_seconds;

        $queue = $this->longRunningTaskLogItem->queue;

        dispatch($job)
            ->onQueue($queue)
            ->delay($delay);
    }

    public function uniqueId(): string
    {
        return $this->longRunningTaskLogItem->id;
    }
}
