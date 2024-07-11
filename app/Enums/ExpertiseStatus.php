<?php

namespace App\Enums;

enum ExpertiseStatus: string
{
    case Canceled = 'canceled';
    case Done = 'done';
    case WaitingValidateFromOthers = 'waiting_validate_from_others';
    case Waiting = 'waiting';
}
