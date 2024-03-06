<?php

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks\LongRunningTestTask;
use Illuminate\Support\Facades\Bus;
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
