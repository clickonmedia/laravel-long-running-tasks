<?php

namespace Clickonmedia\LongRunningTasks;

use Carbon\Carbon;
use Clickonmedia\LongRunningTasks\Enums\LogItemStatus;
use Clickonmedia\LongRunningTasks\Enums\TaskResult;
use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;
use Clickonmedia\LongRunningTasks\Support\Config;
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

    public function queue(string $queue): self
    {
        $this->queue = $queue;

        return $this;
    }

    public function checkFrequencyInSeconds(int $seconds): self
    {
        $this->checkFrequencyInSeconds = $seconds;

        return $this;
    }

    public function keepCheckingForInSeconds(int $seconds)
    {
        $this->keepCheckingForInSeconds = $seconds;

        return $this;
    }

    public function start(?array $meta = null): LongRunningTaskLogItem
    {
        if ($meta) {
            $this->meta($meta);
        }

        $logModel = Config::getLongRunningTaskLogItemModelClass();

        $logItem = $logModel::create([
            'status' => LogItemStatus::Pending,
            'queue' => $this->getQueue(),
            'meta' => $this->meta,
            'type' => $this->type(),
            'check_frequency_in_seconds' => $this->getCheckFrequencyInSeconds(),
            'attempt' => 1,
            'stop_checking_at' => $this->stopCheckingAt(),
        ]);

        $jobClass = Config::getTaskJobClass();

        dispatch(new $jobClass($logItem));

        return $logItem;
    }

    protected function type(): string
    {
        return static::class;
    }

    protected function getCheckFrequencyInSeconds(): int
    {
        if (isset($this->checkFrequencyInSeconds)) {
            return $this->checkFrequencyInSeconds;
        }

        return config('long-running-tasks.default_check_frequency_in_seconds');
    }

    public function getQueue(): string
    {
        if (isset($this->queue)) {
            return $this->queue;
        }

        return config('long-running-tasks.queue');
    }

    public function stopCheckingAt(): Carbon
    {
        $timespan = config('long-running-tasks.keep_checking_for_in_seconds');

        if (isset($this->keepCheckingForInSeconds)) {
            $timespan = $this->keepCheckingForInSeconds;
        }

        return now()->addSeconds($timespan);
    }
}
