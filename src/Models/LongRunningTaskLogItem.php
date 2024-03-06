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
        'last_check_started_at' => 'timestamp',
        'last_check_ended_at' => 'timestamp',
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

    protected function markAsPending(): self
    {
        $this->update([
            'status' => LogItemStatus::Pending,
        ]);

        return $this;
    }

    protected function markAsRunning(): self
    {
        $this->update([
            'last_check_started_at' => now(),
            'status' => LogItemStatus::Running,
        ]);

        return $this;
    }

    public function markAsCheckedEnded(LogItemStatus $logItemStatus): self
    {
        $this->update([
            'last_check_ended_at' => now(),
            'status' => $logItemStatus,
        ]);

        return $this;
    }
}
