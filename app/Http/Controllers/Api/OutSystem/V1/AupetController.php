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
    ) {

        $asaas = AupetAsaasService::getCustomer($cpf);
        $solidyAupet = AupetService::getCustomerInIlevaSolidyDatabase($cpf);

        if (empty($solidyAupet) && empty($asaas)) {

            return response()->json([], 404);
        }

        if (!empty($solidyAupet)) {
            $solidyAupet[0]['activated'] = true;
            return response()->json(
                $solidyAupet[0]
            );
        }

        return response()->json(
            $solidyAupet[0] ?? $asaas
        );
    }
}
