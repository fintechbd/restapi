<?php

use Fintech\RestApi\Http\Controllers\Core\ApiLogController;
use Fintech\RestApi\Http\Controllers\Core\ConfigurationController;
use Fintech\RestApi\Http\Controllers\Core\EncryptedKeyController;
use Fintech\RestApi\Http\Controllers\Core\FailedJobController;
use Fintech\RestApi\Http\Controllers\Core\JobController;
use Fintech\RestApi\Http\Controllers\Core\PackageRegisteredController;
use Fintech\RestApi\Http\Controllers\Core\SettingController;
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

if (Config::get('fintech.core.enabled')) {
    Route::prefix('core')->name('core.')->group(function () {
        Route::get('session-token', EncryptedKeyController::class)->name('session-token');
        Route::get('packages', PackageRegisteredController::class)->name('packages');
    });

    Route::prefix('core')->name('core.')
        ->middleware(config('fintech.auth.middleware'))
        ->group(function () {
            Route::apiResource('settings', SettingController::class);
            Route::post('settings/{setting}/restore', [SettingController::class, 'restore'])->name('settings.restore');

            Route::apiResource('configurations', ConfigurationController::class)->only(['show', 'update', 'destroy']);

            Route::apiResource('jobs', JobController::class)->only(['index', 'show', 'destroy']);

            Route::apiResource('api-logs', ApiLogController::class)->only(['index', 'show', 'destroy']);

            Route::post('failed-jobs/prune', [FailedJobController::class, 'prune'])->name('failed-jobs.prune');
            Route::apiResource('failed-jobs', FailedJobController::class)->only(['index', 'show', 'destroy']);
            Route::post('failed-jobs/{failed_job}/retry', [FailedJobController::class, 'retry'])->name('failed-jobs.retry');

            //DO NOT REMOVE THIS LINE//
        });
}
