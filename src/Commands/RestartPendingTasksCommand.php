<?php

namespace Clickonmedia\LongRunningTasks\Commands;

use Clickonmedia\LongRunningTasks\Enums\LogItemStatus;
use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;
use Clickonmedia\LongRunningTasks\Support\Config;
use Illuminate\Console\Command;

class RestartPendingTasksCommand extends Command
{
    public $signature = 'long-running-tasks:restart';

    public function handle(): int
    {
        $this->info('Starting long running tasks...');

        $logItems = Config::getLongRunningTaskLogItemModelClass();

        $logItems::query()
            ->where('status', LogItemStatus::Pending)
            ->each(function (LongRunningTaskLogItem $logItem) {
                $this->comment("Dispatching job for log item {$logItem->id}...");

                $logItem->dispatchJob();
            });

        $this->info('All done!');

        return self::SUCCESS;
    }
}
