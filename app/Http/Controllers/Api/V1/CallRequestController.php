<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BikerStatus;
use App\Enums\CallRequestStatus;
use App\Enums\CallStatus;
use App\Enums\ExpertiseType;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssociateResource;
use App\Http\Resources\CallResource;
use App\Models\Associate;
use App\Models\Biker;
use App\Models\Call;
use App\Models\CallRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Number;


class CallRequestController extends Controller
{
    public function accept(Call $call, Biker $biker, CallRequest $callRequest, Request $request)
    {
        if (in_array($callRequest->status, [CallRequestStatus::NotAnsewered->value, CallRequestStatus::Denied->value])) {
            return response()->json(['message' => 'Tempo para aceitar o chamado expirado.'], 400);
        }

        $callRequest->update([
            'biker_id' => $biker->id,
            'status' => CallRequestStatus::Accepted->value
        ]);

        $distance = Biker::selectRaw(
            '
        ST_Distance_Sphere(POINT(?, ?), biker_geolocations.location) AS distance',
            [
                $call->location->longitude,
                $call->location->latitude
            ]
        )
            ->find($biker->id)
            ->distance;

        $distanceInKm = $distance / 1000;

        $estimatedTimeArrival = now()->addMinutes(Number::format($distanceInKm * 4.2, precision: 0));

        $call->update([
            'biker_id' => $biker->id,
            'status' => CallStatus::WaitingArrival->value,
            'biker_accepted_at' => now(),
            'estimated_time_arrival' => $estimatedTimeArrival,
        ]);

        $biker->update([
            'status' => BikerStatus::Busy->value
        ]);

        $call->with(['expertises' => function ($query) {
            $query->where('type', ExpertiseType::Secondary);
        }])->get();

        return response()->json(new CallResource($call), 200);
    }
}
