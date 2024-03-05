<?php

namespace Spatie\Monitor\Enums;

enum LogItemCheckResult: string
{
    case ContinueChecking = 'continueChecking';
    case StopChecking = 'stopChecking';
}
