<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssociateCarResource extends JsonResource
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
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'plate' => $this->plate,
            'year' => $this->year,
            'associate' => new AssociateResource($this->associate),
        ];
    }
}
