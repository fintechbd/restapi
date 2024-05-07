<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\RestApi\Http\Requests\Business\ServiceCurrencyRateRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceCostResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CurrencyRateCalculateController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     */
    public function __invoke(ServiceCurrencyRateRequest $request): JsonResponse|ServiceCostResource
    {
        $inputs = $request->all();

        try {
            $roles = config('fintech.auth.customer_roles', [7]);

            $inputs['role_id'] = array_shift($roles);

            $exchangeRate = Business::serviceStat()->cost($inputs);

            return new ServiceCostResource($exchangeRate);

        } catch (Exception $exception) {
            return $this->failed($exception->getMessage());
        }
    }
}
