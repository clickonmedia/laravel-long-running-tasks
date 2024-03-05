<?php

namespace Spatie\Monitor\Enums;

enum LogItemStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Failed = 'failed';
    case DidNotCompleted = 'didNotComplete';
}
