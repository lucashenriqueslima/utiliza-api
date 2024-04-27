<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FipeBrandResource;
use App\Models\Ileva\IlevaFipeBrand;
use Illuminate\Http\Request;

class FipeBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexByVehicleTypeAndName(string $vehicleType, string $name)
    {
        return response()->json( 
        FipeBrandResource::collection(
            IlevaFipeBrand::where('tipo', $vehicleType)
                ->where('nome', 'like', "%$name%")
                ->get(['codigo', 'nome'])
        )
        );
    }
}
