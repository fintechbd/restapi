<?php

use Fintech\Core\Facades\Core;
use Fintech\RestApi\Http\Controllers\Business\ChargeBreakDownController;
use Fintech\RestApi\Http\Controllers\Business\CountryServiceController;
use Fintech\RestApi\Http\Controllers\Business\CurrencyRateCalculateController;
use Fintech\RestApi\Http\Controllers\Business\CurrencyRateController;
use Fintech\RestApi\Http\Controllers\Business\PackageTopChartController;
use Fintech\RestApi\Http\Controllers\Business\RoleServiceController;
use Fintech\RestApi\Http\Controllers\Business\ServiceController;
use Fintech\RestApi\Http\Controllers\Business\ServiceFieldController;
use Fintech\RestApi\Http\Controllers\Business\ServicePackageController;
use Fintech\RestApi\Http\Controllers\Business\ServiceSettingController;
use Fintech\RestApi\Http\Controllers\Business\ServiceStatController;
use Fintech\RestApi\Http\Controllers\Business\ServiceTypeController;
use Fintech\RestApi\Http\Controllers\Business\ServiceVendorController;
use Fintech\RestApi\Http\Controllers\Business\ServiceVendorServiceController;
use Fintech\RestApi\Http\Controllers\Business\ServingCountryController;
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

if (Config::get('fintech.business.enabled')) {

    Route::prefix('business')->name('business.')
        ->middleware(config('fintech.auth.middleware'))
        ->group(function () {
            Route::get('service-settings/types', [ServiceSettingController::class, 'serviceSettingTypes'])
                ->name('service-settings.types');
            Route::get('service-settings/type-fields', [ServiceSettingController::class, 'serviceSettingTypeFields'])
                ->name('service-settings.type-fields');
            Route::apiResource('service-settings', ServiceSettingController::class);
            //             Route::post('service-settings/{service_setting}/restore', [ServiceSettingController::class, 'restore'])->name('service-settings.restore');

            Route::get('service-types/service-type-list', [ServiceTypeController::class, 'serviceTypeList'])
                ->name('service-types.service-type-list');
            Route::apiResource('service-types', ServiceTypeController::class);
            //             Route::post('service-types/{service_type}/restore', [ServiceTypeController::class, 'restore'])->name('service-types.restore');

            Route::apiResource('services', ServiceController::class);
            //             Route::post('services/{service}/restore', [ServiceController::class, 'restore'])->name('services.restore');
            Route::post('services/calculate-cost', [ServiceController::class, 'cost'])
                ->name('services.cost');

            Route::get('service-stats/destination-country-list', [ServiceStatController::class, 'serviceStatWiseCountry'])
                ->name('service-stats.destination-country-list');
            Route::apiResource('service-stats', ServiceStatController::class);
            //             Route::post('service-stats/{service_stat}/restore', [ServiceStatController::class, 'restore'])->name('service-stats.restore');

            Route::apiResource('service-packages', ServicePackageController::class);
            //             Route::post('service-packages/{service_package}/restore', [ServicePackageController::class, 'restore'])->name('service-packages.restore');
            Route::post('service-packages/sync', [ServicePackageController::class, 'sync'])->name('service-packages.restore');

            Route::apiResource('charge-break-downs', ChargeBreakDownController::class);
            //             Route::post('charge-break-downs/{charge_break_down}/restore', [ChargeBreakDownController::class, 'restore'])->name('charge-break-downs.restore');

            Route::apiResource('service-vendors', ServiceVendorController::class);
            //             Route::post('service-vendors/{service_vendor}/restore', [ServiceVendorController::class, 'restore'])->name('service-vendors.restore');

            Route::apiResource('package-top-charts', PackageTopChartController::class);
            //             Route::post('package-top-charts/{package_top_chart}/restore', [PackageTopChartController::class, 'restore'])->name('package-top-charts.restore');

            Route::apiResource('currency-rates', CurrencyRateController::class);
            //             Route::post('currency-rates/{currency_rate}/restore', [CurrencyRateController::class, 'restore'])->name('currency-rates.restore');

            if (Core::packageExists('Auth')) {
                Route::apiResource('role-services', RoleServiceController::class)
                    ->only(['show', 'update']);
            }
            if (Core::packageExists('MetaData')) {
                Route::apiResource('country-services', CountryServiceController::class)
                    ->only(['show', 'update']);
                Route::get('serving-countries', ServingCountryController::class)
                    ->name('services.serving-countries');
            }

            Route::apiResource('service-vendor-services', ServiceVendorServiceController::class)
                ->only(['show', 'update']);
            Route::apiResource('service-fields', ServiceFieldController::class);
            //             Route::post('service-fields/{service_field}/restore', [ServiceFieldController::class, 'restore'])->name('service-fields.restore');

            //DO NOT REMOVE THIS LINE//

            Route::prefix('charts')->name('charts.')->group(function () {
                Route::get('service-rate-charges', \Fintech\RestApi\Http\Controllers\Business\Charts\ServiceRateCostController::class)
                    ->name('service-rate-charges');
            });
        });

    Route::prefix('dropdown')->name('business.')->group(function () {
        Route::get('currency-convert-rate', CurrencyRateCalculateController::class)->name('currency-convert-rate');
        Route::get('service-types', [ServiceTypeController::class, 'dropdown'])->name('service-types.dropdown');
        Route::get('services', [ServiceController::class, 'dropdown'])->name('services.dropdown');
        Route::get('service-stats', [ServiceStatController::class, 'dropdown'])->name('service-stats.dropdown');
        Route::get('service-packages', [ServicePackageController::class, 'dropdown'])->name('service-packages.dropdown');
        Route::get('service-vendors', [ServiceVendorController::class, 'dropdown'])->name('service-vendors.dropdown');
    });
}
