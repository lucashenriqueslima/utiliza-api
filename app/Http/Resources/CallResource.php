<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'address' => $this->address,
            'latitude' => $this->location->latitude,
            'longitude' => $this->location->longitude,
            'accepted_at' => $this->biker_accepted_at,
            'status' => $this->status->value,
            'observation' => $this->observation,
            'main_expertise_status' => $this->status->value,
            'secondary_expertise_status' => 'waiting_main_expertise',
            'associate_car' => new AssociateCarResource($this->associateCar),
        ];
    }
}
