<?php

namespace App\Services;

use App\Enums\CallStatus;
use App\Http\Resources\ExpertiseFileValidaionErrorResource;
use App\Models\Call;
use App\Models\ExpertiseFileValidationError;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpertiseFileValidationErrorService
{

    public function getValidationErrors(): AnonymousResourceCollection
    {
        $validationErrors = Call::select('id', 'biker_id')
            ->where('status', CallStatus::WaitingBikerSeeValidation)
            ->with('validationErrors', function ($query) {
                $query->select('id', 'call_id', 'expertise_file_id', 'error_message')
                    ->whereNull('status')
                    ->with('expertiseFile', function ($query) {
                        $query->select('id', 'file_expertise_type', 'expertise_id')
                            ->with('expertise', function ($query) {
                                $query->select('id', 'app_expertise_index', 'person_type');
                            });
                    });
            })
            ->get();

        return ExpertiseFileValidaionErrorResource::collection(
            $validationErrors
        );
    }
}
