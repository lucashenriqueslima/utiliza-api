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
use Illuminate\Http\Request;

class ExpertiseController extends Controller
{
    private ExpertiseService $service;
    public function __construct()
    {
        $this->service = new ExpertiseService();
    }
    public function store(Request $request, Call $call): void
    {

        // dd($request);

        $expertise = $call->expertises()->create(
            [
                'type' => $request->type,
                'person_type' => $request->person_type,
                'app_expertise_index' => $request->index,
            ]
        );

        if ($request->type === ExpertiseType::Main->value) {
            $this->service->handleExpertiseMainFormFiles($request->expertise_files, $expertise);
            $expertise->formInputs()->create(
                [
                    'field_type' => 'report_text',
                ]
            );
        }

        $expertise->update(['status' => ExpertiseStatus::Waiting->value]);
        $call->update(['status' => CallStatus::WaitingValidation->value]);
        // if ($request->person_type === ExpertisePersonType::Associate) {
        // } else {
        //     $call->thirdParty()->create($request->all());
        // }
    }
}
