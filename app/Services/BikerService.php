<?php

namespace App\Services;

use App\Enums\BikerStatus;
use App\Models\Biker;
use Illuminate\Database\Eloquent\Collection;

class BikerService
{
    public function updateStatus(Biker $biker, BikerStatus $bikerStatus): void
    {
        $biker->update(['status' => $bikerStatus->value]);
    }
}
