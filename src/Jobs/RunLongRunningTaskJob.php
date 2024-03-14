<?php

namespace Clickonmedia\LongRunningTasks\Jobs;

use Clickonmedia\LongRunningTasks\Enums\LogItemStatus;
use Clickonmedia\LongRunningTasks\Enums\TaskResult;
use Clickonmedia\LongRunningTasks\Events\DispatchingNewRunEvent;
use Clickonmedia\LongRunningTasks\Events\TaskCompletedEvent;
use Clickonmedia\LongRunningTasks\Events\TaskDidNotCompleteEvent;
use Clickonmedia\LongRunningTasks\Events\TaskRunEndedEvent;
use Clickonmedia\LongRunningTasks\Events\TaskRunStartingEvent;
use Clickonmedia\LongRunningTasks\LongRunningTask;
use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;
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

    public function __construct(public LongRunningTaskLogItem $longRunningTaskLogItem)
    {

    }

    public function handle()
    {
        $task = $this->longRunningTaskLogItem->task();

        $this->longRunningTaskLogItem->markAsRunning();

        try {
            event(new TaskRunStartingEvent($this->longRunningTaskLogItem));

            $checkResult = $task->check($this->longRunningTaskLogItem);

            event(new TaskRunEndedEvent($this->longRunningTaskLogItem));
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

            event(new TaskCompletedEvent($this->longRunningTaskLogItem));

            return;
        }

        if (! $this->longRunningTaskLogItem->shouldKeepChecking()) {
            $this->longRunningTaskLogItem->markAsCheckedEnded(LogItemStatus::DidNotComplete);

            event(new TaskDidNotCompleteEvent($this->longRunningTaskLogItem));

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

        event(new DispatchingNewRunEvent($this->longRunningTaskLogItem));

        $job = new static($this->longRunningTaskLogItem);

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
