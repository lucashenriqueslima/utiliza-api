<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BikerStatus;
use App\Enums\CallStatus;
use App\Http\Controllers\Controller;
use App\Models\Biker;
use App\Models\Call;
use App\Models\CallRequest;
use App\Services\Firebase\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BikerController extends Controller
{
    public function index(Call $call)
    {

        $bikers = DB::select(
            'SELECT bikers.id,
            firebase_token,
            ST_Distance_Sphere(POINT(?, ?), location) AS distance 
            FROM biker_geolocations 
            INNER JOIN bikers ON bikers.id = biker_geolocations.biker_id
            WHERE bikers.status = ? 
            AND biker_geolocations.updated_at > NOW() - INTERVAL 1 MINUTE
            ORDER BY distance', 
        [
            $call->location->latitude,
            $call->location->longitude,
            BikerStatus::Avaible->value
        ]



        );

        foreach ($bikers as $biker) {
            (new FirebaseService())->sendPushNotification(
                $biker->firebase_token
            );

            if($call->refresh()->status != CallStatus::InService->value) {
                CallRequest::create([
                    'call_id' => $call->id,
                    'biker_id' => $biker->id
                ]);
                continue;
            }

            break;
        }
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
            'status' => 'required|in:avaible,not_avaible',
        ]);

        $biker->update(
            $request->only('status')
        );

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
