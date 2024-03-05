<?php

namespace Spatie\Monitor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Monitor\Monitor
 */
class Monitor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Spatie\Monitor\Monitor::class;
    }
}
