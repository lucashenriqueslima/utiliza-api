<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use Illuminate\Database\Eloquent\Builder;

class BillController extends Controller
{
    public function indexByBikerId(string $bikerId)
    {
        $bills = Bill::select('call_id', 'status', 'due_date', 'value', 'payment_vouncher_file_path')
            ->whereHas('call', function (Builder $query) use ($bikerId) {
                $query->where('biker_id', $bikerId);
            })
            ->get();



        return response()->json(
            BillResource::collection($bills)
        );
    }
}
