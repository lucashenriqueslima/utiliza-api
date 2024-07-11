<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpertiseFileValidaionErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'app_index' => $this->expertiseFile->expertise->app_expertise_index,
            'file_type' => $this->expertiseFile->file_expertise_type,
            'error_message' => $this->error_message,
        ];
    }
}
