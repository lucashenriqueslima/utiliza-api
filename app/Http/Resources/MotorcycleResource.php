<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MotorcycleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'locavibe_motorcycle_id' => $this->id_vehicle_locavibe,
            'plate' => $this->plate,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'chassi' => $this->chassi,
            'renavam' => $this->renavam,
            'fipe_code' => $this->fipe_code,
        ];
    }
}
