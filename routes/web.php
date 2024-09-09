<?php

use App\Http\Controllers\AccidentController;
use App\Http\Controllers\Api\V1\CallController;
use Illuminate\Support\Facades\Route;

//redirect to /admin
Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/acidente-pericia/{encryptedKey}', App\Livewire\AccidentExpertise::class)->name('accident-expertise');
Route::get('/call/{call}/download', [CallController::class, 'download'])->name('call.download');
Route::get('/accident/{accidentId}/download', [AccidentController::class, 'download'])->name('accident.download');
