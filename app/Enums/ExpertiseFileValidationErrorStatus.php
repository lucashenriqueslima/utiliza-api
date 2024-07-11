<?php

namespace App\Enums;

enum ExpertiseFileValidationErrorStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Read = 'read';
}
