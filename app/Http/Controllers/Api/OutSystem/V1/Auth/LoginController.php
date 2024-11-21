<?php

namespace App\Http\Controllers\Api\OutSystem\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\OutSystemToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        try {

            $request->validate([
                'token' => 'required|string',
            ]);

            $outSystem = OutSystemToken::where('token', $request->token)
                ->firstOrFail();

            Auth::guard('api_out_system')->loginUsingId($outSystem->id);

            return response()->json([
                'token' => $outSystem->createToken('outSystemAuthToken')->plainTextToken
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(status: 404);
        }
    }
}
