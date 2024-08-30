<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BikerStatus;
use App\Enums\CallRequestStatus;
use App\Enums\CallStatus;
use App\Http\Controllers\Controller;
use App\Models\Biker;
use App\Models\BikerChangeCall;
use App\Models\Call;
use App\Models\CallRequest;
use App\Services\Firebase\FirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BikerController extends Controller
{
    public function index(Call $call)
    {
        // $bikers = DB::select(
        //     'SELECT bikers.id,
        //     firebase_token,
        //     ST_Distance_Sphere(POINT(?, ?), location) AS distance
        //     FROM biker_geolocations
        //     INNER JOIN bikers ON bikers.id = biker_geolocations.biker_id
        //     WHERE bikers.status = ?
        //     -- AND biker_geolocations.updated_at > NOW() - INTERVAL 100 MINUTE
        //     ORDER BY distance',
        //     [
        //         $call->location->longitude,
        //         $call->location->latitude,
        //         BikerStatus::Avaible->value
        //     ]

        // );
        // $firebaseService = new FirebaseService($call);

        // foreach ($bikers as $biker) {

        //     dd($biker->distance);
        //     $callRequest = CallRequest::create([
        //         'call_id' => $call->id,
        //         'biker_id' => $biker->id,
        //     ]);

        //     $firebaseService->sendPushNotification(
        //         $callRequest->id,
        //         $biker->firebase_token,
        //         number_format($biker->distance / 1000, 1),
        //         Carbon::createFromFormat('Y-m-d H:i:s', $callRequest->created_at)->addSeconds(8)
        //     );

        //     sleep(10);

        //     if ($callRequest->refresh()->status == CallRequestStatus::Accepted->value) {
        //         break;
        //     }

        //     $callRequest->update([
        //         'status' => CallRequestStatus::NotAnsewered->value
        //     ]);
        // }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function updateStatus(Request $request, Biker $biker)
    {
        $request->validate([
            'status' => 'required|in:avaible,not_avaible,busy',
            'has_changed_biker' => 'required|boolean',
        ]);

        $biker->update(
            $request->only('status')
        );

        if ($request->has_changed_biker === true) {
            BikerChangeCall::where('biker_id', $biker->id)
                ->update([
                    'is_delivered' => true
                ]);
        }

        return response(status: 200);
    }

    public function updateFirebaseToken(Request $request, Biker $biker)
    {
        $request->validate([
            'firebase_token' => 'required|string',
        ]);

        $biker->update(
            $request->only('firebase_token')
        );

        return response(status: 200);
    }

    public function destroy(string $id)
    {
        //
    }
}
