<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Resources\ExpertiseFileValidaionErrorResource;
use App\Models\Call;
use App\Models\ExpertiseFileValidationError;

class ExpertiseFileValidationErrorController extends Controller
{
    public function index(Call $call)
    {
        $validationErrors = ExpertiseFileValidationError::where('call_id', $call->id)->get();

        return response()->json(ExpertiseFileValidaionErrorResource::collection(
            $validationErrors
        ));
    }
}
