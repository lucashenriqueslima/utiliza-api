<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BikerChangeCallStatus: string
{
    case Pending = 'pending';
    case Received = 'received';
}
