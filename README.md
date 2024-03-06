# Monitor long running tasks in a Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/clickonmedia/laravel-long-running-tasks-monitor.svg?style=flat-square)](https://packagist.org/packages/clickonmedia/laravel-long-running-tasks-monitor)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/clickonmedia/laravel-long-running-tasks-monitor/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/clickonmedia/laravel-long-running-tasks-monitor/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/clickonmedia/laravel-long-running-tasks-monitor/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/clickonmedia/laravel-long-running-tasks-monitor/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/clickonmedia/laravel-long-running-tasks-monitor.svg?style=flat-square)](https://packagist.org/packages/clickonmedia/laravel-long-running-tasks-monitor)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

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
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-long-running-tasks-monitor-views"
```

## Usage

```php
$monitor = new Clickonmedia\Monitor();
echo $monitor->echoPhrase('Hello, Clickonmedia!');
```

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
