<?php

namespace Clickonmedia\Monitor;

use Carbon\Carbon;
use Clickonmedia\Monitor\Enums\LogItemCheckResult;
use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Exception;

abstract class LongRunningTask
{
    protected array $meta = [];

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
        return now()->addHour();
    }

    abstract public function check(LongRunningTaskLogItem $logItem): LogItemCheckResult;

    abstract public function onFail(LongRunningTaskLogItem $logItem, Exception $exception): ?LogItemCheckResult;
}
