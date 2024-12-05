<?php

namespace App\Http\Resources;

use App\Models\CallValue;
use App\Services\BikerGeolocationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class CallRequestNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $distanceInKm = (new BikerGeolocationService())->getDistanceByBikerId(
            $this->call->location->latitude,
            $this->call->location->longitude,
            $this->biker_id
        ) / 1000;

        return [
            'path' => '/dashboard',
            'call_id' => $this->call_id,
            'call_request_id' => $this->id,
            'address' => $this->call->address,
            'distance' => Number::format($distanceInKm * 1.5, precision: 1),
            'time' => (string) Number::format($distanceInKm * 3.5, precision: 0),
            'price' => (new CallValue())->getValidValueAttribute(),
            'time_limit_to_accept' => Carbon::now()->addSeconds(26),
            'timeout_response' => (string) Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->addSeconds(25),
        ];
    }
}
