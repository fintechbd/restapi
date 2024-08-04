<?php

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
if (Config::get('fintech.card.enabled')) {
    Route::prefix('card')->name('card.')
        ->middleware(config('fintech.auth.middleware'))
        ->group(function () {
            Route::apiResource('instant-cards', \Fintech\RestApi\Http\Controllers\Card\InstantCardController::class);
            Route::post('instant-cards/{instant_card}/restore', [\Fintech\RestApi\Http\Controllers\Card\InstantCardController::class, 'restore'])->name('instant-cards.restore');

            //DO NOT REMOVE THIS LINE//
        });
}
