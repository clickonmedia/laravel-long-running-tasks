<?php

namespace Clickonmedia\Monitor;

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;

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

    abstract public function check(LongRunningTaskLogItem $logItem);

    abstract public function onFail(LongRunningTaskLogItem $logItem);
}
