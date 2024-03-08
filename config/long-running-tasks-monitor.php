<?php

return [
    'keep_checking_for_in_seconds' => 60 * 5,

    'default_check_frequency_in_seconds' => 10,

    'log_model' => Clickonmedia\Monitor\Models\LongRunningTaskLogItem::class,

    'task_job' => Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob::class,
];
