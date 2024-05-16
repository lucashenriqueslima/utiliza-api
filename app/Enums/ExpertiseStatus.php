<?php

namespace App\Enums;

enum ExpertiseStatus: string
{
    case Canceled = 'canceled';
    case Done = 'done';
    case Waiting = 'waiting';
}
