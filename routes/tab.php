<?php

use Fintech\Tab\Http\Controllers\PayBillController;
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
if (Config::get('fintech.tab.enabled')) {
    Route::prefix('tab')->name('tab.')->group(function () {

        Route::apiResource('pay-bills', PayBillController::class);
        Route::post('pay-bills/{pay_bill}/restore', [PayBillController::class, 'restore'])->name('pay-bills.restore');

        //DO NOT REMOVE THIS LINE//
    });
}
