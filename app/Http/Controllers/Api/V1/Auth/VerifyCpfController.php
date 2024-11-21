<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\BikerStatus;
use App\Http\Controllers\Controller;
use App\Jobs\Auth\SendAuthenticationTokenJob;
use App\Models\Biker;
use App\Models\Locavibe\LocavibeRenter;
use App\Services\Auth\AuthService;
use App\Services\Auth\GenerateAuthenticationToken;
use App\Services\Auth\SendAuthenticationToken;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Facades\Octane;

class VerifyCpfController extends Controller
{
    public function store(Request $request, AuthService $authService)
    {
        $request->validate([
            'cpf' => 'required|string|size:14'
        ]);

        try {
            $partiner = Biker::where('cpf', $request->cpf)->first();

            $partiner ??=  $authService->updateOrCreatePartinerByLocavibeRenter(LocavibeRenter::where('cpf', $request->cpf)->first());

            if (!$partiner) {
                return response()->json([
                    'message' => 'CPF não encontrado'
                ], 404);
            }

            if ($partiner?->status === BikerStatus::Banned) {
                return response()->json([
                    'message' => 'Usuário banido'
                ], 403);
            }

            $authToken = $authService->generateAuthenticationToken();

            SendAuthenticationTokenJob::dispatch($partiner, $authToken);

            $authService->saveMailAuthToken($partiner, $authToken);

            $maskedEmail = $authService->maskEmail($partiner->email);

            return response()->json([
                'masked_email' => $maskedEmail
            ]);
        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return response()->json([
                'message' => 'Erro ao gerar token de autenticação'
            ], 500);
        }
    }
}
