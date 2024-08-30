<?php

namespace App\Http\Controllers;

use App\Models\BikerChangeCall;
use Illuminate\Support\Facades\Auth;

class BikerChangeCallController extends Controller
{
    //
    public function showReason(string $callId)
    {

        $bikerChangeCall = BikerChangeCall::where('call_id', $callId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$bikerChangeCall) {
            return response()->json(['reason' => ''], 404);
        }

        return response()->json(['reason' => $bikerChangeCall->reason]);
    }
}
