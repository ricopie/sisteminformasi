<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiary\Http\Controllers\DeleteBeneficiaryController;
use Modules\Beneficiary\Http\Controllers\RegisterBeneficiaryController;
use Modules\Beneficiary\Http\Controllers\UpdateBeneficiaryController;

Route::prefix('beneficiary')->group(function () {
    Route::post('/', RegisterBeneficiaryController::class);
    Route::put('/{id}', UpdateBeneficiaryController::class);
    Route::delete('/{id}', DeleteBeneficiaryController::class);
});
