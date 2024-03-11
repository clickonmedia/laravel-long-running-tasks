<?php

return [
    // TODO: make queue configurable on task

    /*
     * Behind the scenes, this packages use a queue to call tasks.
     * Here you can choose the queue that should be used by default.
     */
    'queue' => 'default',

    /*
     * If a task determines that it should be continued, it will
     * be called again after this amount of time
     */
    'default_check_frequency_in_seconds' => 10,

    /*
     * When a task is not completed in this amount of time,
     * it will not run again, and marked as `didNotComplete`.
     */
    'keep_checking_for_in_seconds' => 60 * 5,


    // TODO: make this setting work
    /*
     * The model that will be used by default to track
     * the status of all tasks.
     */
    'log_model' => Clickonmedia\Monitor\Models\LongRunningTaskLogItem::class,

    // TODO: make this setting work
    /*
     * The job responsible for calling tasks.
     */
    'task_job' => Clickonmedia\Monitor\Jobs\RunLongRunningTaskJob::class,
];
