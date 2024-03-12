<?php

use Clickonmedia\Monitor\Exceptions\InvalidModel;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Clickonmedia\Monitor\Support\Config;

it('can handle a valid model', function () {
    $customModel = new class extends LongRunningTaskLogItem
    {
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
