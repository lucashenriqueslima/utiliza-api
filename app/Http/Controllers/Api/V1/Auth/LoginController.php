<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\MotorcycleResource;
use App\Http\Resources\BikerResource;
use App\Models\Locavibe\LocavibeRenter;
use App\Services\Auth\UpdateBikerAndMotorcycleFields;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|size:14',
            'auth_token' => 'required',
        ]);
        
        try {
            $renter = LocavibeRenter::where('cpf', $request->cpf)
                ->where('authToken', $request->auth_token)
                ->where('authTokenVerified', false)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Token inválido ou expirado. Por favor, solicite um novo token ou aguarde.'
            ], 404);
        }

        $biker = UpdateBikerAndMotorcycleFields::run($renter);
        
        Auth::loginUsingId($biker->id);

        $renter->authTokenVerified = true;
        $renter->authToken = null;
        $renter->save();

        return response()->json([
            'biker' => new BikerResource($biker),
        ], 200);
    }
}
