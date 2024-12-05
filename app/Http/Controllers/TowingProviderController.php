<?php

namespace App\Http\Controllers;

use App\Models\TowingProvider;
use Illuminate\Http\Request;

class TowingProviderController extends Controller
{
    public function store(Request $request)
    {
        return request()->json(
            $request->all(),
        )

        $towingProvider = new TowingProvider();
        $towingProvider->fantasy_name = $request->fantasy_name;
        $towingProvider->cnpj = $request->cnpj;
        $towingProvider->email = $request->email;
        $towingProvider->phone = $request->phone;
        $towingProvider->city = $request->city;
        $towingProvider->uf = $request->uf;
        $towingProvider->save();

        return $towingProvider;
    }
}
