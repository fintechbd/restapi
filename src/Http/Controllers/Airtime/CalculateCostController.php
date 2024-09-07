<?php

namespace Fintech\RestApi\Http\Controllers\Airtime;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Business\Facades\Business;
use Fintech\Core\Abstracts\BaseModel;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Fintech\RestApi\Http\Requests\Tab\PayBillRequest;
use Fintech\RestApi\Http\Resources\Tab\PayBillCostResource;
use Fintech\Tab\Facades\Tab;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class CalculateCostController extends Controller
{
    /**
     * @lrd:start
     */
    public function __invoke(PayBillRequest $request): PayBillCostResource|JsonResponse
    {
        $inputs = $request->validated();

        try {

            if ($request->missing('user_id')) {
                $inputs['user_id'] = $request->user('sanctum')->id;
            }

            if ($user = Auth::user()->find($inputs['user_id'])) {
                $inputs['role_id'] = $user->roles->first()?->getKey() ?? null;
            }

            $service = Business::service()->find($inputs['service_id']);

            $vendor = Business::serviceVendor()->find($service->service_vendor_id);

            $inputs['service_vendor_id'] = $vendor?->getKey() ?? null;

            $quote = new BaseModel;

            $quote->source_country_id = $inputs['source_country_id'];
            $quote->destination_country_id = $inputs['destination_country_id'];
            $quote->service_vendor_id = $vendor->getKey();
            $quote->service_id = $inputs['service_id'];
            $quote->user_id = $inputs['user_id'];
            $quote->vendor = $inputs['vendor'] ?? $vendor->service_vendor_slug;
            $quote->status = OrderStatus::Pending->value;
            $quote->order_data = [
                'pay_bill_data' => $inputs['pay_bill_data'],
                'service_stat_data' => $inputs,
            ];
            $quote->order_number = 'CANPB'.Str::padLeft(time(), 15, '0');
            $quote->is_refunded = 'no';

            $quoteInfo = Tab::assignVendor()->requestQuote($quote);

            $inputs['amount'] = 3000;

            $exchangeRate = Business::serviceStat()->cost($inputs);

            $exchangeRate['vendor_info'] = $quoteInfo;

            return new PayBillCostResource($exchangeRate);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }

    }
}
