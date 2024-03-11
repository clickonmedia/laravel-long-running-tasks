<?php

return [
    /*
     * When a task is not completed in this amount of time,
     * it will not run again, and marked as `didNotComplete`.
     */
    'keep_checking_for_in_seconds' => 60 * 5,

    /*
     * If a task determines that it should be continued, it will
     * be called again after this amount of time
     */
    'default_check_frequency_in_seconds' => 10,

    /*
     * The model that will be used by default to track
     * the status of all tasks.
     */
    'log_model' => Clickonmedia\Monitor\Models\LongRunningTaskLogItem::class,

    /*
     * The job responsible for calling tasks.
     */
    'task_job' => Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob::class,
];
