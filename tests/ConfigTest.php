<?php

use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\Exceptions\InvalidModel;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Clickonmedia\Monitor\Support\Config;
use Clickonmedia\Monitor\Tests\TestSupport\LongRunningTasks\LongRunningTestTask;
use Illuminate\Support\Facades\Queue;
use Clickonmedia\Monitor\LongRunningTask;

it('can handle a valid custom model', function () {
    $customModel = new class extends LongRunningTaskLogItem {
        protected $table = 'long_running_task_log_items';
    };

    config()->set('long-running-tasks-monitor.log_model', $customModel::class);

    $modelClass = Config::getLongRunningTaskLogItemModelClass();

    expect($modelClass)->toBe($customModel::class);
});

it('will throw an exception for an invalid model', function () {
    config()->set('long-running-tasks-monitor.log_model', Config::class);

    Config::getLongRunningTaskLogItemModelClass();
})->throws(InvalidModel::class);

it('can use a custom model', function() {
    $customModel = new class extends LongRunningTaskLogItem {
        protected $table = 'long_running_task_log_items';
    };

    config()->set('long-running-tasks-monitor.log_model', $customModel::class);

    $task = new class($customModel) extends LongRunningTask
    {
        public function __construct(protected string $customModel)
        {

        }

        public function check(LongRunningTaskLogItem $logItem): TaskResult
        {
            if (! $logItem instanceof $this->customModel) {
                throw new Exception('Not the right class');
            }
        }
    };

    $task->start();
})->todo();

it('can handle a custom job class', function () {
    $logItem = LongRunningTaskLogItem::factory()->create();

    $customJob = new class($logItem) extends RunLongRunningTaskJob {

    };

    config()->set('long-running-tasks-monitor.task_job', $customJob::class);

    $jobClass = Config::getTaskJobClass();

    expect($jobClass)->toBe($customJob::class);
});

it('will use a custom job class', function() {
    Queue::fake();

    $logItem = LongRunningTaskLogItem::factory()->create();

    $customJob = new class($logItem) extends RunLongRunningTaskJob {

    };

    config()->set('long-running-tasks-monitor.task_job', $customJob::class);

    LongRunningTestTask::make()->start();

    Queue::assertPushed($customJob::class);
});
