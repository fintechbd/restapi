<?php

use Fintech\RestApi\Http\Controllers\MetaData\CatalogController;
use Fintech\RestApi\Http\Controllers\MetaData\CityController;
use Fintech\RestApi\Http\Controllers\MetaData\CountryController;
use Fintech\RestApi\Http\Controllers\MetaData\CountryCurrencyController;
use Fintech\RestApi\Http\Controllers\MetaData\CurrencyController;
use Fintech\RestApi\Http\Controllers\MetaData\DropDownController;
use Fintech\RestApi\Http\Controllers\MetaData\LanguageController;
use Fintech\RestApi\Http\Controllers\MetaData\RegionController;
use Fintech\RestApi\Http\Controllers\MetaData\StateController;
use Fintech\RestApi\Http\Controllers\MetaData\SubRegionController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
if (Config::get('fintech.metadata.enabled')) {
    Route::prefix('metadata')->name('metadata.')
        ->middleware(config('fintech.auth.middleware'))
        ->group(function () {
            Route::apiResource('regions', RegionController::class);
            Route::post('regions/{region}/restore', [RegionController::class, 'restore'])->name('regions.restore');

            Route::apiResource('subregions', SubRegionController::class);
            Route::post('subregions/{subregion}/restore', [SubRegionController::class, 'restore'])->name('subregions.restore');

            Route::apiResource('countries', CountryController::class);
            Route::post('countries/{country}/restore', [CountryController::class, 'restore'])->name('countries.restore');
            Route::get('countries/{country}/toggle-serving', [CountryController::class, 'toggleServingCountry'])->name('countries.toggle-serving');

            Route::apiResource('states', StateController::class);
            Route::post('states/{state}/restore', [StateController::class, 'restore'])->name('states.restore');

            Route::apiResource('cities', CityController::class);
            Route::post('cities/{city}/restore', [CityController::class, 'restore'])->name('cities.restore');

            Route::apiResource('languages', LanguageController::class)->only(['index', 'update', 'show']);
            Route::get('languages/{language}/toggle', [LanguageController::class, 'toggle'])->name('languages.toggle');

            Route::apiResource('catalogs', CatalogController::class);
            Route::post('catalogs/{catalog}/restore', [CatalogController::class, 'restore'])->name('catalogs.restore');

            Route::apiResource('currencies', CurrencyController::class)->only(['index', 'update', 'show']);
            Route::get('currencies/{currency}/toggle', [CurrencyController::class, 'toggle'])->name('currencies.toggle');

            Route::apiResource('country-currencies', CountryCurrencyController::class)->only(['show', 'update']);

            //DO NOT REMOVE THIS LINE//
        });

    Route::prefix('dropdown')->name('metadata.')->group(function () {

        Route::get('regions', [RegionController::class, 'dropdown'])->name('regions.dropdown');
        Route::get('subregions', [SubRegionController::class, 'dropdown'])->name('subregions.dropdown');
        Route::get('countries', [CountryController::class, 'dropdown'])->name('countries.dropdown');
        Route::get('languages', [LanguageController::class, 'dropdown'])->name('languages.dropdown');
        Route::get('currencies', [CurrencyController::class, 'dropdown'])->name('currencies.dropdown');
        Route::get('states', [StateController::class, 'dropdown'])->name('states.dropdown');
        Route::get('cities', [CityController::class, 'dropdown'])->name('cities.dropdown');

        Route::controller(DropDownController::class)->group(function () {
            Route::get('phone-codes', 'phoneCode')->name('phone-codes.dropdown');
            Route::get('nationalities', 'nationality')->name('nationalities.dropdown');
            Route::get('id-doc-types', 'idDocType')->name('id-doc-types.dropdown');
            Route::get('fund-sources', 'fundSource')->name('fund-sources.dropdown');
            Route::get('occupations', 'occupation')->name('occupations.dropdown');
            Route::get('remittance-purposes', 'remittancePurpose')->name('remittance-purposes.dropdown');
            Route::get('relations', 'relation')->name('relations.dropdown');
            Route::get('catalog-types', 'catalogType')->name('catalog-types.dropdown');
            Route::get('genders', 'gender')->name('genders.dropdown');
            Route::get('blood-groups', 'bloodGroup')->name('blood-groups.dropdown');
            Route::get('marital-statuses', 'maritalStatus')->name('marital-statuses.dropdown');
            Route::get('proof-of-addresses', 'proofOfAddress')->name('proof-of-addresses.dropdown');
        });
    });
}
