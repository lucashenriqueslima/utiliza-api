<?php

namespace App\Http\Controllers\Api\OutSystem\V1;

use App\Http\Controllers\Controller;
use App\Models\OutSystemToken;
use App\Services\Asaas\AupetAsaasService;
use App\Services\Aupet\AupetService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Facades\Octane;


class AupetController extends Controller
{
    public function associateHasAupetBenefitActive(
        string $cpf,
        AupetAsaasService $aupetAsaasService,
        AupetService $aupetService,
    ) {

        $asaas = $aupetAsaasService->associateHasAupetBenefitActive($cpf);
        $solidyAupet = $aupetService->associateHasAupetBenefitActive($cpf);

        if (!in_array(true, [$asaas, $solidyAupet])) {

            return response()->json([
                'success' => false,
                'message' => 'Associado não possui benefício AUPET',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Associado possui benefício AUPET',
        ]);
    }
}
