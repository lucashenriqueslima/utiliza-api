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
            'call_id' => $this->id,
            'biker_id' => $this->biker_id,
            'validation_errors' => $this->validationErrors->map(function ($validationError) {
                return [
                    'error_message' => $validationError->error_message,
                    'app_expertise_index' => (string) $validationError->expertiseFile->expertise->app_expertise_index,
                    'file_expertise_type' => $validationError->expertiseFile->file_expertise_type,
                    'person_type' => $validationError->expertiseFile->expertise->person_type,
                    'expertise_file' => [
                        'id' => $validationError->expertiseFile->id,
                        'file_expertise_type' => $validationError->expertiseFile->file_expertise_type,
                        'expertise' => [
                            'id' => $validationError->expertiseFile->expertise->id,
                            'app_expertise_index' => $validationError->expertiseFile->expertise->app_expertise_index,
                            'person_type' => $validationError->expertiseFile->expertise->person_type,
                        ],
                    ],
                ];
            }),
        ];
    }
}
