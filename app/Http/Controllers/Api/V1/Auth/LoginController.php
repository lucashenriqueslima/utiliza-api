<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\MotorcycleResource;
use App\Http\Resources\BikerResource;
use App\Models\Biker;
use App\Models\Locavibe\LocavibeRenter;
use App\Services\Auth\AuthService;
use App\Services\Auth\UpdateBikerAndMotorcycleFields;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request, AuthService $authService)
    {
        $request->validate([
            'cpf' => 'required|string|size:14',
            'auth_token' => 'required',
        ]);

        try {
            $partiner = Biker::where('cpf', $request->cpf)
                ->where('auth_token', $request->auth_token)
                ->where('auth_token_verified', false)
                ->firstOrFail();

            Auth::loginUsingId($partiner->id);

            $partiner->auth_token_verified = true;
            $partiner->auth_token = null;
            $partiner->save();

            $authService->handlePartinerNewGeolocation($partiner);

            return response()->json(new BikerResource($partiner), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Token inválido ou expirado. Por favor, solicite um novo token ou aguarde.'
            ], 404);
        }
    }
}
