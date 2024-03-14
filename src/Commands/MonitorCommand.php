<?php

namespace Clickonmedia\LongRunningTasks\Commands;

use Illuminate\Console\Command;

class MonitorCommand extends Command
{
    public $signature = 'long-running-tasks:monitor';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
