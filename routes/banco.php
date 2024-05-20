<?php

use Fintech\RestApi\Http\Controllers\Banco\BankAccountController;
use Fintech\RestApi\Http\Controllers\Banco\BankBranchController;
use Fintech\RestApi\Http\Controllers\Banco\BankController;
use Fintech\RestApi\Http\Controllers\Banco\BeneficiaryController;
use Fintech\RestApi\Http\Controllers\Banco\BeneficiaryTypeController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "API" middleware group. Enjoy building your API!
|
*/

if (Config::get('fintech.banco.enabled')) {
    Route::prefix('banco')->name('banco.')
        ->middleware(config('fintech.auth.middleware'))
        ->group(function () {
            Route::get('banks/bank-categories', [BankController::class, 'bankCategory'])->name('banks.bank-categories');
            Route::apiResource('banks', BankController::class);
            Route::post('banks/{bank}/restore', [BankController::class, 'restore'])->name('banks.restore');

            Route::apiResource('bank-branches', BankBranchController::class);
            Route::post('bank-branches/{bank_branch}/restore', [BankBranchController::class, 'restore'])->name('bank-branches.restore');

            Route::apiResource('beneficiaries', BeneficiaryController::class);
            Route::post('beneficiaries/{beneficiary}/restore', [BeneficiaryController::class, 'restore'])->name('beneficiaries.restore');

            Route::apiResource('beneficiary-types', BeneficiaryTypeController::class);
            Route::post('beneficiary-types/{beneficiary_type}/restore', [BeneficiaryTypeController::class, 'restore'])->name('beneficiary-types.restore');

            Route::apiResource('bank-accounts', BankAccountController::class);
            Route::post('bank-accounts/{bank_account}/restore', [BankAccountController::class, 'restore'])->name('bank-accounts.restore');

            Route::apiResource('beneficiary-account-types', \Fintech\RestApi\Http\Controllers\Banco\BeneficiaryAccountTypeController::class);
            Route::post('beneficiary-account-types/{beneficiary_account_type}/restore', [\Fintech\RestApi\Http\Controllers\Banco\BeneficiaryAccountTypeController::class, 'restore'])->name('beneficiary-account-types.restore');

            //DO NOT REMOVE THIS LINE//
        });
}
