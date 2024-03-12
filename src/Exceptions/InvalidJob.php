<?php

namespace Clickonmedia\Monitor\Exceptions;

use Exception;

class InvalidJob extends Exception
{
    public static function make(string $class): self
    {
        $baseJobClass = InvalidJob::class;

        return new static("The job class `{$class}` does not extend the `{$baseJobClass}` base job class.");
    }
}
