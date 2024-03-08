<?php

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks\LongRunningTestTask;
use Illuminate\Support\Facades\Queue;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    LongRunningTestTask::$checkClosure = null;

    TestTime::freeze('Y-m-d H:i:s', '2024-01-01 00:00:00');

    $this->logItem = LongRunningTaskLogItem::factory()->create([
        'status' => LogItemStatus::Pending,
        'type' => LongRunningTestTask::class,
        'check_frequency_in_seconds' => 10,
    ]);
});

it('can handle a pending task that will complete', function () {
    Queue::fake();

    (new RunLongRunningTaskJob($this->logItem))->handle();

    expect($this->logItem->refresh())
        ->status->toBe(LogItemStatus::Completed)
        ->attempt->toBe(1)
        ->run_count->toBe(1);

    Queue::assertNothingPushed();
});

it('can handle a pending task that needs a couple of runs to complete', function () {
    LongRunningTestTask::$checkClosure = function (LongRunningTaskLogItem $logItem) {
        return $logItem->run_count < 5
            ? TaskResult::ContinueChecking
            : TaskResult::StopChecking;
    };

    (new RunLongRunningTaskJob($this->logItem))->handle();

    expect($this->logItem->refresh())
        ->run_count->toBe(5)
        ->status->toBe(LogItemStatus::Completed)
        ->last_check_started_at->not()->toBeNull()
        ->last_check_ended_at->not()->toBeNull();
});

it('will handle exceptions well', function () {
    LongRunningTestTask::$checkClosure = function (LongRunningTaskLogItem $logItem) {
        throw new Exception();
    };

    (new RunLongRunningTaskJob($this->logItem))->handle();

    dd($this->logItem->refresh());
})->skip();
