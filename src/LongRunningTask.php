<?php

namespace Clickonmedia\Monitor;

use Carbon\Carbon;
use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Enums\TaskResult;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Exception;

abstract class LongRunningTask
{
    protected array $meta = [];

    abstract public function check(LongRunningTaskLogItem $logItem): TaskResult;

    public function onFail(LongRunningTaskLogItem $logItem, Exception $exception): ?TaskResult
    {
        return TaskResult::StopChecking;
    }

    public static function make()
    {
        return new static();
    }

    public function meta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function start(): LongRunningTaskLogItem
    {
        $logItem = LongRunningTaskLogItem::create([
            'status' => LogItemStatus::Pending,
            'meta' => $this->meta,
            'type' => $this->type(),
            'check_frequency_in_seconds' => $this->checkFrequencyInSeconds(),
            'attempt' => 1,
            'stop_checking_at' => $this->stopCheckingAt(),
        ]);

        dispatch(new RunLongRunningTaskJob($logItem));

        return $logItem;
    }

    public function type(): string
    {
        return static::class;
    }

    public function checkFrequencyInSeconds(): int
    {
        return 10;
    }

    public function stopCheckingAt(): Carbon
    {
        return now()->addSeconds(config('long-running-tasks-monitor.keep_checking_for_in_seconds'));
    }
}
