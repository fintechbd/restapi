<?php

use Fintech\Card\Models\InstantCard;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Fintech\RestApi\Http\Controllers\Card\InstantCardController;

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
            Route::post('instant-cards/{instant_card}/restore', [InstantCardController::class, 'restore'])->name('instant-cards.restore');
            Route::post('instant-cards/{instant_card}/status', [InstantCardController::class, 'status'])->name('instant-cards.status');
            Route::apiResource('instant-cards', InstantCardController::class);
            //DO NOT REMOVE THIS LINE//
        });
    Route::prefix('dropdown')->name('card.')->group(function () {
        Route::get('instant-cards', [InstantCardController::class, 'dropdown'])->name('users.dropdown');
        Route::get('instant-card-statuses', [InstantCardController::class, 'statusDropdown'])->name('user-statuses.dropdown');    
    });
}
