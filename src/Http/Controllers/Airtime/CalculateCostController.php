<?php

namespace Fintech\RestApi\Http\Controllers\Airtime;

use Exception;
use Fintech\Airtime\Exceptions\AirtimeException;
use Fintech\Airtime\Facades\Airtime;
use Fintech\Auth\Facades\Auth;
use Fintech\Business\Facades\Business;
use Fintech\Core\Abstracts\BaseModel;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Fintech\RestApi\Http\Requests\Airtime\AirtimeCostRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceCostResource;
use Fintech\RestApi\Http\Resources\Business\ServicePackageCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class CalculateCostController extends Controller
{
    /**
     * @lrd:start
     */
    public function __invoke(AirtimeCostRequest $request): ServiceCostResource|JsonResponse
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
                'airtime_data' => $inputs['airtime_data'],
                'service_stat_data' => $inputs,
            ];
            $quote->order_number = 'CANVR'.Str::padLeft(time(), 15, '0');
            $quote->is_refunded = 'no';

            $quoteInfo = Airtime::assignVendor()->requestQuote($quote);

            if ($quoteInfo['status'] === false) {
                throw new AirtimeException(__('airtime::messages.assign_vendor.quote_failed'));
            }

            $inputs['amount'] = $quoteInfo['amount'];

            $exchangeRate = Business::serviceStat()->cost($inputs);

            $exchangeRate['vendor_info'] = $quoteInfo;

            $servicePackages = Business::servicePackage()->list([
                'service_id' => $inputs['service_id'],
                'country_id' => $inputs['destination_country_id'],
                'enabled' => true,
                'paginate' => false,
                'sort' => 'amount',
                'direction' => 'asc',
                'connection_type' => $inputs['airtime_data']['connection_type'] ?? 'prepaid',
                'near_amount' => $inputs['amount'],
                'limit' => 3,
            ]);

            $exchangeRate['offers'] = new ServicePackageCollection($servicePackages);

            return new ServiceCostResource($exchangeRate);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }

    }
}
