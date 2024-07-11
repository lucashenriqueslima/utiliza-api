<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CallStatus;
use App\Enums\ExpertisePersonType;
use App\Enums\ExpertiseStatus;
use App\Enums\ExpertiseType;
use App\Enums\S3Prefix;
use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Expertise;
use App\Services\ExpertiseService;
use App\Services\ThirdParttyService;
use App\Services\ThirdPartyService;
use Illuminate\Http\Request;

class ExpertiseController extends Controller
{

    public function store(Request $request, Call $call, ExpertiseService $expertiseService): void
    {

        $expertise = $call->expertises()->create(
            [
                'type' => $request->type,
                'person_type' => $request->person_type,
                'app_expertise_index' => $request->index,
            ]
        );

        // if ($request->person_type === ExpertiseType::Secondary->value) {
        //     $expertise->update(['cnpj' => $request->cnpj]);
        // }
        if ($request->person_type === ExpertisePersonType::ThirdParty->value) {
            $expertiseService->handleExpertiseThirdPartyFormTexts($request, $expertise);
        }

        if ($request->type === ExpertiseType::Main->value) {
            $expertiseService->handleExpertiseMainFormFiles($request, $expertise);
        }

        $expertise->update(['status' => ExpertiseStatus::Waiting]);
        $call->update(['status' => CallStatus::WaitingValidation]);
    }
}
