<?php

namespace Spatie\Monitor\Commands;

use Illuminate\Console\Command;

class MonitorCommand extends Command
{
    public $signature = 'laravel-long-running-tasks-monitor';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
