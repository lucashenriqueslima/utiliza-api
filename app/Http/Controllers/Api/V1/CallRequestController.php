<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\CallRequestDTO;
use App\DTO\CallRequestNotificationDTO;
use App\Enums\BikerStatus;
use App\Enums\CallRequestStatus;
use App\Enums\CallStatus;
use App\Enums\ExpertiseType;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssociateResource;
use App\Http\Resources\CallRequestNotificationResource;
use App\Http\Resources\CallResource;
use App\Models\Associate;
use App\Models\Biker;
use App\Models\Call;
use App\Models\CallRequest;
use App\Services\BikerGeolocationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Number;


class CallRequestController extends Controller
{

    public function store(string $encryptedCall, string $bikerId, Request $request, BikerGeolocationService $bikerGeolocationService)
    {
        try {

            $decryptedKey = explode('|', Crypt::decrypt($encryptedCall));

            $call = Call::where('id', $decryptedKey[0])
                ->where('created_at', $decryptedKey[1])
                ->firstOrFail();

            $callRequest = CallRequest::create([
                'call_id' => $call->id,
                'biker_id' => $bikerId,
            ]);

            return response()->json(
                new CallRequestNotificationResource($callRequest->load('call')),
                201
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Chamado não encontrado.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => ''], 404);
        }
    }
    public function accept(Call $call, Biker $biker, CallRequest $callRequest, Request $request)
    {
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

        $biker->update([
            'status' => BikerStatus::Busy->value
        ]);

        $call->with(['expertises' => function ($query) {
            $query->where('type', ExpertiseType::Secondary);
        }])->get();

        return response()->json(new CallResource($call), 200);
    }
}
