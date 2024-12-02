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
use App\Services\BikerService;
use Illuminate\Http\Request;
use Illuminate\Support\Number;


class CallRequestController extends Controller
{
    public function accept(
        Call $call,
        Biker $biker,
        CallRequest $callRequest,
        Request $request,
        BikerService $bikerService
    ) {
        // $request->validate([
        //     'app_version' => 'sometimes|string',
        // ]);

        // if (empty($request->app_version) || $request->app_version != '1.0.1') {
        //     return response()->json(['message' => 'Aplicativo desatualizado, entre em contato com atendente (62 9250-9220) e solicite a nova versão do APP'], 406);
        // }


        if (in_array($callRequest->status, [CallRequestStatus::NotAnsewered->value, CallRequestStatus::Denied->value])) {
            return response()->json(['message' => 'Tempo para aceitar o chamado expirado.'], 400);
        }

        if ($call->status !== CallStatus::SearchingBiker) {
            return response()->json(['message' => 'Chamado não está mais disponível.'], 400);
        }

        $callRequest->update([
            'biker_id' => $biker->id,
            'status' => CallRequestStatus::Accepted->value
        ]);

        $distance = Biker::selectRaw(
            '
        bikers.id,
        ST_Distance_Sphere(POINT(?, ?), biker_geolocations.location) AS distance',
            [
                $call->location->longitude,
                $call->location->latitude
            ]
        )
            ->join('biker_geolocations', 'bikers.id', '=', 'biker_geolocations.biker_id')
            ->find($biker->id)
            ->distance;

        $distanceInKm = $distance / 1000;

        $estimatedTimeArrival = now()->addMinutes((int) Number::format($distanceInKm * 5, precision: 0));

        $call->update([
            'biker_id' => $biker->id,
            'status' => CallStatus::WaitingArrival->value,
            'biker_accepted_at' => now(),
            'estimated_time_arrival' => $estimatedTimeArrival,
        ]);

        $bikerService->updateStatus($biker, BikerStatus::Busy);

        $call->with(['expertises' => function ($query) {
            $query->where('type', ExpertiseType::Secondary);
        }])->get();

        return response()->json(new CallResource($call), 200);
    }
}
