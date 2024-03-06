<?php

namespace Clickonmedia\Monitor\Models;

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\LongRunningTask;
use Illuminate\Database\Eloquent\Model;

class LongRunningTaskLogItem extends Model
{
    public $guarded = [];

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
