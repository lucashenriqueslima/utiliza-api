<?php

use App\Http\Controllers\Api\V1\CallController;
use App\Jobs\UpdateAuvoCustomerJob;
use App\Jobs\UpdateFieldControlCustomerJob;
use App\Jobs\UpdateFieldControlCustomerPhoneNumberJob;
use App\Models\Call;
use App\Models\Ileva\IlevaAssociateVehicle;
use App\Services\Auvo\AuvoService;
use App\Services\FieldControl\FieldControlService;
use App\Services\S3\S3Service;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use STS\ZipStream\Facades\Zip;
use STS\ZipStream\Models\S3File;
use Illuminate\Support\Facades\Response as Download;

// Route::get('/auvo', function () {
//     $auvoService = new AuvoService();
//     [$solidyCustomers, $motoclubCustomers, $novaCustomers] = $auvoService->getIlevaDatabaseCustomers();

//     $auvoService->updateCustomers($solidyCustomers);
//     $auvoService->updateCustomers($motoclubCustomers, 'mc');
//     $auvoService->updateCustomers($novaCustomers, 'nv');
// });

// Route::get('/field-control', function () {
//     $customers = IlevaAssociateVehicle::getVehiclesForFieldControl();

//     foreach ($customers as $customer) {
//         UpdateFieldControlCustomerJob::dispatch($customer);
//     }
// });


//redirect to /admin
Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/acidente-pericia/{encryptedKey}', App\Livewire\AccidentExpertise::class)->name('accident-expertise');
Route::get('/call/{call}/download', [CallController::class, 'download'])->name('call.download');
