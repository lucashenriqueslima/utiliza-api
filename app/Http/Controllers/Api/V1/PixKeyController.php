<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Biker;
use App\Models\PixKey;
use Illuminate\Http\Request;

class PixKeyController extends Controller
{

    public function show(string $bikerId)
    {
        $pixKey = PixKey::where('biker_id', $bikerId)
            ->where('is_active', true)
            ->first();

        if (!$pixKey) {
            return response()->json(status: 404);
        }

        return response()->json($pixKey);
    }

    public function store(Request $request, string $bikerId)
    {
        $request->validate([
            'key' => 'required|string',
            'type' => 'required|string|in:cpf,cnpj,phone,email',
        ]);

        PixKey::where('biker_id', $bikerId)
            ->update([
                'is_active' => false
            ]);

        $pixKey = PixKey::create([
            'key' => $request->key,
            'type' => $request->type,
            'biker_id' => $bikerId,
        ]);

        return response()->json($pixKey, 201);
    }
}
