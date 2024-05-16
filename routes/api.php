<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\BikerGeolocationController;
use App\Http\Controllers\Api\V1\CallRequestController;
use App\Http\Controllers\Api\V1\FipeModelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\BikerController;
use App\Http\Controllers\Api\V1\ExpertiseController;
use App\Http\Controllers\Api\V1\FipeBrandController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {

        Route::post('/verify-email', [VerifyEmailController::class, 'store']);
        Route::post('/login', [LoginController::class, 'store']);
        Route::post('/associate/{associateId}/call', [LoginController::class, 'destroy']);
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::patch('/biker/{biker}/status', [BikerController::class, 'updateStatus']);
        Route::patch('/biker/{biker}/firebase-token', [BikerController::class, 'updateFirebaseToken']);

        Route::put('/biker/{id}/geolocation', [BikerGeolocationController::class, 'update']);

        Route::post('/call/{call}/biker/{biker}/call-request/{callRequest}/accept', [CallRequestController::class, 'accept']);

        Route::get('/fipe/brand/{vehicleType}/{name}', [FipeBrandController::class, 'indexByVehicleTypeAndName']);

        Route::post('auth/logout', [LogoutController::class, 'destroy']);
        Route::get('/fipe/brand/{brandId}/model/{name?}', [FipeModelController::class, 'indexByBrandIdAndName']);

        Route::post('call/{call}/expertise/create', [ExpertiseController::class, 'store']);
    });
});
