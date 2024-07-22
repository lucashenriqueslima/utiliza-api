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
                'main_expertise_group' => $request->main_expertise_group,
            ]
        );

        if ($request->person_type === ExpertisePersonType::ThirdParty->value) {
            $expertiseService->handleExpertiseThirdPartyFormTexts($request, $expertise, $call->id);
        }

        if ($request->type === ExpertiseType::Main->value) {
            $expertiseService->handleExpertiseMainFormFiles($request, $expertise);
        } else {
            $expertiseService->handleExpertiseSecondaryFormFiles($request, $expertise);
            $expertise->update(['status' => ExpertiseStatus::Done]);
        }

        // if ($request->person_type != ExpertiseType::Secondary->value) {
        //     $call->update(['status' => CallStatus::WaitingValidation]);
        // }
    }
}
