<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\BikerGeolocationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\BikerController;

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
    });

});


