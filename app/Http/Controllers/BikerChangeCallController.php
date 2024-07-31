<?php

namespace App\Http\Controllers;

use App\Models\BikerChangeCall;
use App\Models\Call;
use Illuminate\Http\Request;

class BikerChangeCallController extends Controller
{
    //
    public function showReason(string $callId)
    {

        try {
            return response()->json(['reason' => BikerChangeCall::where('call_id', $callId)
                ->where('user_id', auth()->id())
                ->firstOrFail()->reason]);
        } catch (\Throwable $th) {
            return response()->json(['reason' => ''], 404);
        }

        // $bikerChangeCall = BikerChangeCall::where('call_id', $callId)
        //     ->where('user_id', auth()->id())
        //     ->first();

        // if (!$bikerChangeCall) {
        //     return response()->json(['reason' => ''], 404);
        // }

        // return response()->json(['reason' => $bikerChangeCall->reason]);
    }
}
