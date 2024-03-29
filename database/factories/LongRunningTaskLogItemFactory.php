<?php

namespace Clickonmedia\LongRunningTasks\Database\Factories;

use Clickonmedia\LongRunningTasks\Enums\LogItemStatus;
use Clickonmedia\LongRunningTasks\Models\LongRunningTaskLogItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class LongRunningTaskLogItemFactory extends Factory
{
    protected $model = LongRunningTaskLogItem::class;

    public function definition()
    {
        return [
            'type' => $this->faker->word,
            'queue' => 'default',
            'status' => LogItemStatus::Pending,
            'check_frequency_in_seconds' => 10,
            'meta' => [],
            'stop_checking_at' => now()->addSeconds(config('long-running-tasks.keep_checking_for_in_seconds')),
        ];
    }
}
