<?php

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks\LongRunningTestTask;
use Illuminate\Support\Facades\Queue;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::freeze();

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

it('can handle a pending task, that needs a couple of runs to complete', function () {

});
