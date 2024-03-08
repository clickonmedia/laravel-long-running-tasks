<?php

namespace Clickonmedia\Monitor\Models;

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Exceptions\InvalidTask;
use Clickonmedia\Monitor\LongRunningTask;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LongRunningTaskLogItem extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'status' => LogItemStatus::class,
        'meta' => 'array',
        'last_check_started_at' => 'datetime',
        'last_check_ended_at' => 'datetime',
        'stop_checking_at' => 'datetime',
        'latest_exception' => 'array',
        'run_count' => 'integer',
    ];

    public function task(): LongRunningTask
    {
        $taskClass = $this->type;

        if (! class_exists($taskClass)) {
            throw InvalidTask::classDoesNotExist($taskClass);
        }

        if (! is_a($taskClass, LongRunningTask::class, true)) {
            throw InvalidTask::classIsNotATask($taskClass);
        }

        /** @var LongRunningTask $task */
        return new $taskClass;
    }

    public function markAsPending(): self
    {
        $this->update([
            'status' => LogItemStatus::Pending,
        ]);

        return $this;
    }

    public function markAsRunning(): self
    {
        $this->update([
            'last_check_started_at' => now(),
            'status' => LogItemStatus::Running,
            'run_count' => $this->run_count + 1,
            'latest_exception' => null,
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

    public function shouldKeepChecking(): bool
    {
        if (is_null($this->stop_checking_at)) {
            return true;
        }

        return $this->stop_checking_at > now();
    }
}
