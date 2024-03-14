<?php

namespace Clickonmedia\LongRunningTasks;

use Clickonmedia\LongRunningTasks\Facades\Monitor;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LongRunningTasksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-long-running-tasks')
            ->hasConfigFile()
            ->hasMigration('create_long_running_task_log_items_table')
            ->hasCommand(Monitor::class);
    }
}
