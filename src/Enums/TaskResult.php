<?php

namespace Clickonmedia\Monitor\Enums;

enum TaskResult: string
{
    case ContinueChecking = 'continueChecking';
    case StopChecking = 'stopChecking';
}
