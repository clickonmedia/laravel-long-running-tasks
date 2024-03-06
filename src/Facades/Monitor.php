<?php

namespace Clickonmedia\Monitor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Clickonmedia\Monitor\Monitor
 */
class Monitor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Clickonmedia\Monitor\Monitor::class;
    }
}
