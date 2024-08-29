<?php

namespace Fintech\RestApi\Http\Controllers\Tab;

use App\Http\Controllers\Controller;
use Exception;
use Fintech\Business\Facades\Business;
use Fintech\RestApi\Http\Requests\Tab\PayBillRequest;
use Fintech\RestApi\Http\Resources\Tab\PayBillCostResource;
use Illuminate\Http\JsonResponse;

class CalculateCostController extends Controller
{
    /**
     * @lrd:start
     */
    public function __invoke(PayBillRequest $request): PayBillCostResource|JsonResponse
    {
        $inputs = $request->all();

        try {
            $roles = config('fintech.auth.customer_roles', [7]);

            $inputs['role_id'] = array_shift($roles);

            $exchangeRate = Business::serviceStat()->cost($inputs);

            return new PayBillCostResource($exchangeRate);

        } catch (Exception $exception) {
            return response()->failed($exception);
        }
    }
}
