<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\MetaData\Facades\MetaData;
use Fintech\MetaData\Http\Resources\CountryCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ServingCountryController extends Controller
{
    use ApiResponseTrait;

    public function __invoke(): CountryCollection|JsonResponse
    {
        try {

            $inputs['paginate'] = false;
            $inputs['is_serving'] = true;

            $countryPaginate = MetaData::country()->list($inputs);

            return new CountryCollection($countryPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
