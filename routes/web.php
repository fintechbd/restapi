<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "Web" middleware group. Enjoy building your API!
|
*/

Route::get('test/sslvr-test', [\Fintech\RestApi\Http\Controllers\Airtime\SSLVRController::class,'test']);
