<?php

use App\Jobs\UpdateAuvoCustomerJob;
use App\Services\Auvo\AuvoService;
use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;

Route::get('/', function () {
    // $auvoService = new AuvoService();
    // [$solidyCustomers, $motoclubCustomers] = $auvoService->getIlevaDatabaseCustomers();

    // $auvoService->updateCustomers($solidyCustomers);
    // $auvoService->updateCustomers($motoclubCustomers, 'mc');
});
