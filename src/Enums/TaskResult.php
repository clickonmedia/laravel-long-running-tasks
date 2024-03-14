<?php

namespace Clickonmedia\LongRunningTasks\Enums;

enum TaskResult: string
{
    case ContinueChecking = 'continueChecking';
    case StopChecking = 'stopChecking';
}
