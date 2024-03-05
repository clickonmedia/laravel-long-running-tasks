<?php

namespace Spatie\Monitor\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Monitor\Enums\LogItemStatus;
use Spatie\Monitor\LongRunningTask;

class LongRunningTaskLogItem extends Model
{
    public $casts = [
        'status' => LogItemStatus::class,
        'meta' => 'array',
    ];

    public function task(): LongRunningTask
    {
        $taskClass = $this->type;

        if (! class_exists($taskClass)) {
            // TODO: throw exception
        }

        if (! is_a($taskClass, LongRunningTask::class)) {
            // TODO: throw exception
        }

        /** @var LongRunningTask $task */
        return new $taskClass;
    }
}
