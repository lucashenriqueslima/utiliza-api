<?php

namespace App\Services;

use App\Models\BikerChangeCall;
use Illuminate\Database\Eloquent\Collection;

class BikerChangeCallService
{
    public function getBikerChangeCalls(): Collection
    {
        return BikerChangeCall::select('id', 'biker_id', 'call_id', 'reason')
            ->where('is_delivered', false)
            ->get();
    }
}
