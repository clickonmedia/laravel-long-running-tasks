<?php

namespace Clickonmedia\Monitor\Exceptions;

use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Exception;

class InvalidModel extends Exception
{
    public static function make(string $class): self
    {
        $baseModelClass = LongRunningTaskLogItem::class;

        return new static("The model `{$class}` does not extend the `{$baseModelClass}` base model class.");
    }
}
