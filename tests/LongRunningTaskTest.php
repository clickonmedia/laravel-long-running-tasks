<?php

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks\LongRunningTestTask;
use Illuminate\Support\Facades\Queue;

it('can create create a pending task', function () {
    Queue::fake();

    LongRunningTestTask::make()->start();

    expect(LongRunningTaskLogItem::all())->toHaveCount(1);

    expect(LongRunningTaskLogItem::first())
        ->type->toBe(LongRunningTestTask::class)
        ->status->toBe(LogItemStatus::Pending)
        ->attempt->toBe(1)
        ->check_frequency_in_seconds->toBe(10)
        ->meta->toBe([]);

    Queue::assertPushed(RunLongRunningTaskJob::class);
});

it('can handle a pending task that will complete', function () {
    $task = new class extends LongRunningTask
    {
        public function check(LongRunningTaskLogItem $logItem): TaskResult
        {
            return TaskResult::StopChecking;
        }
    };

    $task->start();

    expect(LongRunningTaskLogItem::first())
        ->status->toBe(LogItemStatus::Completed)
        ->attempt->toBe(1)
        ->run_count->toBe(1);
});

it('can handle a pending task that needs a couple of runs to complete', function () {
    $task = new class extends LongRunningTask
    {
        public function check(LongRunningTaskLogItem $logItem): TaskResult
        {
            return $logItem->run_count < 5
                ? TaskResult::ContinueChecking
                : TaskResult::StopChecking;
        }
    };

    $task->start();

    expect(LongRunningTaskLogItem::first())
        ->run_count->toBe(5)
        ->status->toBe(LogItemStatus::Completed)
        ->last_check_started_at->not()->toBeNull()
        ->last_check_ended_at->not()->toBeNull();
});

it('will can handle a task that always fails', function () {
    $task = new class extends LongRunningTask
    {
        public function check(LongRunningTaskLogItem $logItem): TaskResult
        {
            throw new Exception();
        }
    };

    $task->start();

    expect(LongRunningTaskLogItem::first())
        ->status->toBe(LogitemStatus::Failed)
        ->run_count->toBe(1)
        ->latest_exception->toHaveKeys(['message', 'trace']);
});

it('can handle a task that will recover', function () {
    $task = new class extends LongRunningTask
    {
        public function check(LongRunningTaskLogItem $logItem): TaskResult
        {
            if ($logItem->run_count < 3) {
                throw new Exception();
            }

            return TaskResult::StopChecking;
        }

        public function onFail(LongRunningTaskLogItem $logItem, Exception $exception): ?TaskResult
        {
            return TaskResult::ContinueChecking;
        }
    };

    $task->start();

    expect(LongRunningTaskLogItem::first())
        ->status->toBe(LogitemStatus::Completed)
        ->run_count->toBe(3)
        ->latest_exception->toBeNull();
});

it('will stop a task that would run forever', function () {
    config()->set('long-running-tasks-monitor.keep_checking_for_in_seconds', 1);

    $task = new class extends LongRunningTask
    {
        public function check(LongRunningTaskLogItem $logItem): TaskResult
        {
            return TaskResult::ContinueChecking;
        }
    };

    $task->start();

    expect(LongRunningTaskLogItem::first())
        ->status->toBe(LogitemStatus::DidNotComplete)
        ->run_count->toBeGreaterThan(1);
});
