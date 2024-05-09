<?php

use Fintech\RestApi\Http\Controllers\Airtime\BangladeshTopUpController;
use Fintech\RestApi\Http\Controllers\Airtime\InternationalTopUpController;
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

if (Config::get('fintech.airtime.enabled')) {
    Route::prefix('airtime')->name('airtime.')
        ->middleware(config('fintech.auth.middleware'))
        ->group(function () {

            Route::apiResource('bangladesh-top-ups', BangladeshTopUpController::class)
                ->only('index', 'store', 'show');

            Route::apiResource('international-top-ups', InternationalTopUpController::class)
                ->only('index', 'store', 'show');

            //DO NOT REMOVE THIS LINE//
        });
}
