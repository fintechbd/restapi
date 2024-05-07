<?php

use Illuminate\Support\Facades\Config;
use \Fintech\Core\Facades\Core;
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
if (Config::get('fintech.restapi.enabled')) {
    if(Core::packageExists('Airtime')) {
        include "airtime.php";
    }

    if(Core::packageExists('Auth')) {
        include "auth.php";
    }

    if(Core::packageExists('Banco')) {
        include "banco.php";
    }

    if(Core::packageExists('Bell')) {
        include "bell.php";
    }

    if(Core::packageExists('Business')) {
        include "business.php";
    }

    if(Core::packageExists('Chat')) {
        include "chat.php";
    }

    if(Core::packageExists('Core')) {
        include "core.php";
    }

    if(Core::packageExists('Ekyc')) {
        include "ekyc.php";
    }

    if(Core::packageExists('Gift')) {
        include "gift.php";
    }

    if(Core::packageExists('MetaData')) {
        include "metadata.php";
    }

    if(Core::packageExists('Promo')) {
        include "promo.php";
    }

    if(Core::packageExists('Reload')) {
        include "reload.php";
    }

    if(Core::packageExists('Remit')) {
        include "remit.php";
    }

    if(Core::packageExists('Sanction')) {
        include "sanction.php";
    }

    if(Core::packageExists('Tab')) {
        include "tab.php";
    }

    if(Core::packageExists('Transaction')) {
        include "transaction.php";
    }
}
