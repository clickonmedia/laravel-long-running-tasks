<?php

use Clickonmedia\LongRunningTasks\Commands\RestartPendingTasksCommand;
use Clickonmedia\LongRunningTasks\Jobs\RunLongRunningTaskJob;
use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;
use Illuminate\Support\Facades\Queue;

it('can restart pending tasks', function () {
    Queue::fake();

    $logItem = LongRunningTaskLogItem::factory()->create();

    $this->artisan(RestartPendingTasksCommand::class)->assertSuccessful();

    Queue::assertPushed(
        RunLongRunningTaskJob::class,
        fn($job) => $job->longRunningTaskLogItem->id === $logItem->id
    );
});
