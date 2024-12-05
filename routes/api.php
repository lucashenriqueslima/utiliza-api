<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\OutSystem\V1\Auth\LoginController as OutSystemLoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\BikerGeolocationController;
use App\Http\Controllers\Api\V1\BillController;
use App\Http\Controllers\Api\V1\CallRequestController;
use App\Http\Controllers\Api\V1\FipeModelController;
use App\Services\Auvo\AuvoService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\VerifyCpfController;
use App\Http\Controllers\Api\V1\BikerController;
use App\Http\Controllers\Api\V1\CallController;
use App\Http\Controllers\Api\V1\ExpertiseController;
use App\Http\Controllers\Api\V1\ExpertiseFileValidationErrorController;
use App\Http\Controllers\Api\V1\FipeBrandController;
use App\Http\Controllers\Api\V1\PixKeyController;
use App\Http\Controllers\BikerChangeCallController;
use App\Models\Accident;
use App\Models\AuvoWorkshop;
use App\Models\Biker;
use App\Models\Bill;
use App\Models\Call;
use App\Models\Dependent;
use App\Models\PixKeyHistory;
use App\Models\TowingProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::post('/towing-providers', function (Request $request) {
    // dd($request->all());

    //return $request->all(); json

    return $request->all();

    $towingProvider = new TowingProvider();
    $towingProvider->fantasy_name = $request->fantasy_name;
    $towingProvider->cnpj = $request->cnpj;
    $towingProvider->email = $request->email;
    $towingProvider->phone = $request->phone;
    $towingProvider->city = $request->city;
    $towingProvider->uf = $request->uf;
    $towingProvider->save();

    return $towingProvider;
});

Route::prefix('auvo')->group(function () {
    Route::get('/workshops', function (): Collection {
        return AuvoWorkshop::select('id', 'auvo_collaborator_id', 'ileva_id', 'visit_time', 'days_of_week')
            ->with('collaborator:id,auvo_id')
            ->get();
    });
});

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {

        Route::post('/verify-cpf', [VerifyCpfController::class, 'store']);
        Route::post('/login', [LoginController::class, 'store']);
        Route::post('/associate/{associateId}/call', [LoginController::class, 'destroy']);
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::patch('/biker/{biker}/status', [BikerController::class, 'updateStatus']);
        Route::patch('/biker/{biker}/firebase-token', [BikerController::class, 'updateFirebaseToken']);

        Route::put('/biker/{id}/geolocation', [BikerGeolocationController::class, 'update']);

        Route::post('/call/{encryptedKey}/biker/{biker}/call-request', [CallRequestController::class, 'store']);

        Route::post('/call/{call}/biker/{biker}/call-request/{callRequest}/accept', [CallRequestController::class, 'accept']);

        Route::get('/fipe/brand/{vehicleType}/{name}', [FipeBrandController::class, 'indexByVehicleTypeAndName']);

        Route::post('auth/logout', [LogoutController::class, 'destroy']);
        Route::get('/fipe/brand/{brandId}/model/{name?}', [FipeModelController::class, 'indexByBrandIdAndName']);

        Route::get('biker/{bikerId}/bills', [BillController::class, 'indexByBikerId']);
        Route::get('call/{id}/status', [CallController::class, 'showStatus']);
        Route::patch('call/{call}', [CallController::class, 'update']);
        Route::post('call/{call}/expertise/create', [ExpertiseController::class, 'store']);

        Route::get('/call/{callId}/expertise/file-validation-errors', [ExpertiseFileValidationErrorController::class, 'index']);

        Route::get('/biker/{biker}/pix-key', [PixKeyController::class, 'show']);
        Route::post('/biker/{biker}/pix-key', [PixKeyController::class, 'store']);

        Route::get('/call/{call}/biker-change-call/reason', [BikerChangeCallController::class, 'showReason']);
    });
});

Route::prefix('out-systems/v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [OutSystemLoginController::class, 'store']);
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('/accidents', function () {
            return Accident::all();
        });

        Route::get('/calls', function () {
            return Call::all();
        });

        Route::get('/bills', function () {
            return Bill::all();
        });

        Route::get('/bikers', function () {
            return Biker::all();
        });

        Route::get('/dependents', function () {
            return Dependent::all();
        });

        Route::post('auth/logout', [LogoutController::class, 'destroy']);
    });
});
