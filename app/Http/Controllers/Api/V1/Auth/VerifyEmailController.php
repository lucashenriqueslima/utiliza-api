<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Locavibe\LocavibeRenter;
use App\Services\Auth\GenerateAuthenticationToken;
use App\Services\Auth\SendAuthenticationToken;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|size:14'
        ]);

        try {
            $renter = LocavibeRenter::where('cpf', $request->cpf)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Email não encontrado'
            ], 404);
        }

        $authToken = GenerateAuthenticationToken::run();

        SendAuthenticationToken::run($renter, $authToken);

        $renter->authToken = (string)$authToken;
        $renter->authTokenVerified = false;

        $renter->save();

        return response(status: 200);
    }
}
