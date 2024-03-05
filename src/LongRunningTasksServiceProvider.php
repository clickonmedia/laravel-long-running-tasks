<?php

namespace Spatie\Monitor;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Monitor\Facades\Monitor;

class LongRunningTasksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-long-running-tasks-monitor')
            ->hasConfigFile()
            ->hasMigration('create_long_running_tasks_table')
            ->hasCommand(Monitor::class);

        LongRunningSomeServiceTask::make()
            ->meta()
            ->start();

    }
}
