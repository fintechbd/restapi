<?php

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

use Illuminate\Support\Facades\Route;

Route::prefix('airtime')->name('airtime.')
    ->middleware(config('fintech.auth.middleware'))
    ->group(function () {

        Route::apiResource('bangladesh-top-ups', \Fintech\RestApi\Http\Controllers\Airtime\BangladeshTopUpController::class)
            ->only('index', 'store', 'show');

        Route::apiResource('international-top-ups', \Fintech\RestApi\Http\Controllers\Airtime\InternationalTopUpController::class)
            ->only('index', 'store', 'show');

        //DO NOT REMOVE THIS LINE//
    });
