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

        [$aupet, $solidyAupet] = Octane::concurrently([
            fn() => AupetService::getCustomerInAupetDatabase($cpf),
            fn() => AupetService::getCustomerInIlevaSolidyDatabase($cpf),
        ]);

        if (empty($solidyAupet) && empty($aupet)) {

            return response()->json([], 404);
        }

        return response()->json(
            $aupet[0] ?? $solidyAupet[0]
        );
    }
}
