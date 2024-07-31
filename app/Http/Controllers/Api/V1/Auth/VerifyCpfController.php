<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\Auth\SendAuthenticationTokenJob;
use App\Models\Locavibe\LocavibeRenter;
use App\Services\Auth\AuthService;
use App\Services\Auth\GenerateAuthenticationToken;
use App\Services\Auth\SendAuthenticationToken;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class VerifyCpfController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|size:14'
        ]);

        try {
            $locavibeRenter = LocavibeRenter::where('cpf', $request->cpf)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'CPF não encontrado'
            ], 404);
        }

        $authToken = AuthService::generateAuthenticationToken();

        SendAuthenticationTokenJob::dispatch($locavibeRenter, $authToken);

        $locavibeRenter->authToken = $authToken;
        $locavibeRenter->authTokenVerified = false;

        $locavibeRenter->save();

        $maskedEmail = AuthService::maskEmail($locavibeRenter->email);

        return response()->json([
            'masked_email' => $maskedEmail
        ]);
    }
}
