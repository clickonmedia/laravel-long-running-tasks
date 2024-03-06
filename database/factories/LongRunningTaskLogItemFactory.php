<?php

namespace Clickonmedia\Monitor\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;

class LongRunningTaskLogItemFactory extends Factory
{
    protected $model = LongRunningTaskLogItem::class;

    public function definition()
    {
        return [
            'type' => $this->faker->word,
            'status' => LogItemStatus::Pending,
            'check_frequency_in_seconds' => 10,
            'meta' => [],
        ];
    }
}
