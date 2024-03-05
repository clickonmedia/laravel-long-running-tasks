<?php

namespace Spatie\Monitor;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Monitor\Commands\MonitorCommand;

class MonitorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-long-running-tasks-monitor')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-long-running-tasks-monitor_table')
            ->hasCommand(MonitorCommand::class);
    }
}
