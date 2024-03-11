# Monitor long running tasks in a Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/clickonmedia/laravel-long-running-tasks-monitor.svg?style=flat-square)](https://packagist.org/packages/clickonmedia/laravel-long-running-tasks-monitor)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/clickonmedia/laravel-long-running-tasks-monitor/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/clickonmedia/laravel-long-running-tasks-monitor/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/clickonmedia/laravel-long-running-tasks-monitor/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/clickonmedia/laravel-long-running-tasks-monitor/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/clickonmedia/laravel-long-running-tasks-monitor.svg?style=flat-square)](https://packagist.org/packages/clickonmedia/laravel-long-running-tasks-monitor)

Some services, like AWS Rekognition, allow you to start a task on their side. Instead of sending a webhook when the task is finished, the services expects you to regularly poll to know when it is finished (or get an updated status).

This package can help you monitor such long-running tasks that are executed externally.

You do so by creating a task like this.

```php
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\LongRunningTask;

class MyTask extends LongRunningTask
{
    public function check(LongRunningTaskLogItem $logItem): TaskResult
    {
        // get some information about this task
        $meta = $logItem->meta
    
        // do some work here
        $allWorkIsDone = /* ... */
       
        // return wheter we should continue the task in a new run
        
         return $allWorkIsDone
            ? TaskResult::StopChecking
            : TaskResult::ContinueChecking
    }
}
```

When `TaskResult::ContinueChecking` is return, this `check` function will be called again in 10 seconds (as defined in the `default_check_frequency_in_seconds` of the config file).

After you have created your task, you can start it like this.

```php
MyTask::make()->meta($anArray)->start();
```

The `check` method of `MyTask` will be called every 10 seconds until it returns `StopChecking`

## Installation

You can install the package via composer:

```bash
composer require clickonmedia/laravel-long-running-tasks-monitor
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-long-running-tasks-monitor-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-long-running-tasks-monitor-config"
```

This is the contents of the published config file:

```php
return [
    /*
     * Behind the scenes, this packages use a queue to call tasks.
     * Here you can choose the queue that should be used by default.
     */
    'queue' => 'default',

    /*
     * If a task determines that it should be continued, it will
     * be called again after this amount of time
     */
    'default_check_frequency_in_seconds' => 10,

    /*
     * When a task is not completed in this amount of time,
     * it will not run again, and marked as `didNotComplete`.
     */
    'keep_checking_for_in_seconds' => 60 * 5,

    /*
     * The model that will be used by default to track
     * the status of all tasks.
     */
    'log_model' => Clickonmedia\Monitor\Models\LongRunningTaskLogItem::class,

    /*
     * The job responsible for calling tasks.
     */
    'task_job' => Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob::class,
];
```

This package make use of queues to call tasks again after a certain amount of time. Make sure you've set up [queues](https://laravel.com/docs/10.x/queues) in your Laravel app.

## Usage

To monitor a long-running task on an external service, you should define a task class. It should extend the `Clickonmedia\Monitor\LongRunningTask` provided by the package.

It's `check` function should perform the work you need it to do and return a `TaskResult`. When returning `TaskResult::StopChecking` the task will not be called again. When returning `TaskResult::ContinueChecking` it will be called again in 10 seconds by default.

```php
use Clickonmedia\Monitor\LongRunningTask;
use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\LongRunningTask;

class MyTask extends LongRunningTask
{
    public function check(LongRunningTaskLogItem $logItem): TaskResult
    {
        // get some information about this task
        $meta = $logItem->meta
    
        // do some work here
        $allWorkIsDone = /* ... */
       
        // return wheter we should continue the task in a new run
        
         return $allWorkIsDone
            ? TaskResult::StopChecking
            : TaskResult::ContinueChecking
    }
}
```

### Adding meta data

### Tracking the status of tasks

### Using a different queue

### Preventing never-ending tasks

### Handling exceptions

### Using your own model

### Using your own job
`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
