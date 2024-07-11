<?php

use App\Jobs\UpdateAuvoCustomerJob;
use App\Jobs\UpdateFieldControlCustomerJob;
use App\Models\Ileva\IlevaAssociateVehicle;
use App\Services\Auvo\AuvoService;
use App\Services\FieldControl\FieldControlService;
use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;

// Route::get('/auvo', function () {
//     $auvoService = new AuvoService();
//     [$solidyCustomers, $motoclubCustomers] = $auvoService->getIlevaDatabaseCustomers();

//     $auvoService->updateCustomers($solidyCustomers);
//     $auvoService->updateCustomers($motoclubCustomers, 'mc');
// });

// Route::get('/field-control', function () {
//     $customers = IlevaAssociateVehicle::getVehiclesForFieldControl();

//     foreach ($customers as $customer) {
//         UpdateFieldControlCustomerJob::dispatch($customer);
//     }
// });
