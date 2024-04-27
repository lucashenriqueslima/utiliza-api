<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FipeModelResource;
use App\Models\Ileva\IlevaFipeModel;
use Illuminate\Http\Request;

class FipeModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexByBrandIdAndName(string $brandId, string $name)
    {
        return response()->json(
            FipeModelResource::collection(
                IlevaFipeModel::where('codigo_marca', $brandId)
                ->where('nome', 'like', "%" . $name . "%")
                ->get(['nome', 'codigo'])
            )
        );
    }
}
