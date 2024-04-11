<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BikerResource extends JsonResource
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
            'locavibe_biker_id' => $this->id_renter_locavibe,
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'phone' => $this->phone,
            'status' => $this->status,
            'api_token' => $this->createToken('authToken')->plainTextToken,
            'motorcycle' => new MotorcycleResource($this->motorcycle),
        ];
    }
}
