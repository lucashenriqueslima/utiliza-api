<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Call;
use Illuminate\Http\Request;

class CallController extends Controller
{

    public function update(Request $request, Call $call)
    {
        $call->update(['status' => $request->status]);
    }
}
