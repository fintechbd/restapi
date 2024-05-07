<?php

namespace Fintech\RestApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * // Crud Service Method Point Do not Remove //
 *
 * @see \Fintech\RestApi\RestApi
 */
class RestApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Fintech\RestApi\RestApi::class;
    }
}
