<?php

namespace Clickonmedia\Monitor\Database\Factories;

use Clickonmedia\Monitor\Enums\LogItemStatus;
use Clickonmedia\Monitor\Models\LongRunningTaskLogItem;
use Illuminate\Database\Eloquent\Factories\Factory;

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
