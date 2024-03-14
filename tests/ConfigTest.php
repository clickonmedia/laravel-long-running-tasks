<?php

use Clickonmedia\LongRunningTasks\Enums\TaskResult;
use Clickonmedia\LongRunningTasks\Exceptions\InvalidModel;
use Clickonmedia\LongRunningTasks\Jobs\RunLongRunningTaskJob;
use Clickonmedia\LongRunningTasks\LongRunningTask;
use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;
use Clickonmedia\LongRunningTasks\Support\Config;
use Clickonmedia\LongRunningTasks\Tests\TestSupport\LongRunningTasks\LongRunningTestTask;
use Illuminate\Support\Facades\Queue;

it('can handle a valid custom model', function () {
    $customModel = new class extends LongRunningTaskLogItem
    {
        protected $table = 'long_running_task_log_items';
    };

    config()->set('long-running-tasks.log_model', $customModel::class);

    $modelClass = Config::getLongRunningTaskLogItemModelClass();

    expect($modelClass)->toBe($customModel::class);
});

it('will throw an exception for an invalid model', function () {
    config()->set('long-running-tasks.log_model', Config::class);

    Config::getLongRunningTaskLogItemModelClass();
})->throws(InvalidModel::class);

it('can use a custom model', function () {
    $customModel = new class extends LongRunningTaskLogItem
    {
        protected $table = 'long_running_task_log_items';
    };

    config()->set('long-running-tasks.log_model', $customModel::class);

    $task = new class() extends LongRunningTask
    {
        public static string $customModel;

        public function check(LongRunningTaskLogItem $logItem): TaskResult
        {
            if (! $logItem instanceof self::$customModel) {
                throw new Exception('Not the right class');
            }

            return TaskResult::StopChecking;
        }
    };

    $task::$customModel = $customModel::class;

    $task->start();

    expect(LongRunningTaskLogItem::first()->latest_exception)->toBeNull();
});

it('can handle a custom job class', function () {
    $logItem = LongRunningTaskLogItem::factory()->create();

    $customJob = new class($logItem) extends RunLongRunningTaskJob
    {
    };

    config()->set('long-running-tasks.task_job', $customJob::class);

    $jobClass = Config::getTaskJobClass();

    expect($jobClass)->toBe($customJob::class);
});

it('will use a custom job class', function () {
    Queue::fake();

    $logItem = LongRunningTaskLogItem::factory()->create();

    $customJob = new class($logItem) extends RunLongRunningTaskJob
    {
    };

    config()->set('long-running-tasks.task_job', $customJob::class);

    LongRunningTestTask::make()->start();

    Queue::assertPushed($customJob::class);
});
