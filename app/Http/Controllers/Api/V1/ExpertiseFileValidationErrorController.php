<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ExpertiseFileValidationErrorStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExpertiseFileValidaionErrorResource;
use App\Models\Call;
use App\Models\ExpertiseFileValidationError;
use Illuminate\Support\Facades\DB;

class ExpertiseFileValidationErrorController extends Controller
{
    public function index(string $callId)
    {
        $validationErrors = ExpertiseFileValidationError::where('call_id', $callId)
            ->with('expertiseFile.expertise')
            ->whereNull('status')
            ->get();

        return response()->json(ExpertiseFileValidaionErrorResource::collection(
            $validationErrors
        ));
    }
}
