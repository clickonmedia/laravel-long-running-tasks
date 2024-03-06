<?php

namespace Clickonmedia\Monitor\Enums;

enum LogItemCheckResult: string
{
    case ContinueChecking = 'continueChecking';
    case StopChecking = 'stopChecking';
}
