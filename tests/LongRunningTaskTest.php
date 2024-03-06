<?php

use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks\LongRunningTestTask;
use Illuminate\Support\Facades\Bus;

it('can create create a pending task', function () {
    Bus::fake();

    LongRunningTestTask::make()->start();

    expect(LongRunningTaskLogItem::all())->toHaveCount(1);
});
