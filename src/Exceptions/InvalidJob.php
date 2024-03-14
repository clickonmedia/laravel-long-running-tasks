<?php

namespace Clickonmedia\LongRunningTasks\Exceptions;

use Clickonmedia\LongRunningTasks\Jobs\RunLongRunningTaskJob;
use Exception;

class InvalidJob extends Exception
{
    public static function make(string $class): self
    {
        $baseJobClass = RunLongRunningTaskJob::class;

        return new static("The job class `{$class}` does not extend the `{$baseJobClass}` base job class.");
    }
}
