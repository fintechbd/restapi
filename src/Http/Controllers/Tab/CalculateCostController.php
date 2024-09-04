<?php

namespace Fintech\RestApi\Http\Controllers\Tab;

use App\Http\Controllers\Controller;
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

            $inputs['amount'] = 100;
            $exchangeRate = Business::serviceStat()->cost($inputs);

            $exchangeRate = json_decode(
                <<<JSON
                {
                    "rate": 85.66996,
                    "input": "CAD",
                    "output": "BDT",
                    "input_unit": "CAD 1.00",
                    "output_unit": "BDT 85.67",
                    "input_symbol": "$",
                    "output_symbol": "\u09f3",
                    "amount": "20",
                    "amount_formatted": "CAD 20.00",
                    "converted": "1713.4",
                    "converted_formatted": "BDT 1,713.40",
                    "charge": "4%",
                    "charge_amount": "0.8",
                    "discount": "6%",
                    "discount_amount": "1.2",
                    "commission": "0",
                    "commission_amount": "0",
                    "total_amount": "19.6",
                    "charge_amount_formatted": "CAD 0.80",
                    "discount_amount_formatted": "CAD 1.20",
                    "commission_amount_formatted": "CAD 0.00",
                    "total_amount_formatted": "CAD 19.60"
                }
           JSON, true);

            $exchangeRate['vendor_info'] = $quoteInfo;

            return new PayBillCostResource($exchangeRate);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }

    }
}
