<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ServiceVendorServiceRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceVendorServiceResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ServiceVendorServiceController extends Controller
{
    /**
     * @lrd:start
     * return services to a specified service vendor resource using id.
     *
     * @lrd:end
     */
    public function show(string|int $id): ServiceVendorServiceResource|JsonResponse
    {
        try {

            $serviceVendor = Business::serviceVendor()->find($id);

            if (! $serviceVendor) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            return new ServiceVendorServiceResource($serviceVendor);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Assign services to a specified service vendor resource using id.
     *
     * @lrd:end
     */
    public function update(ServiceVendorServiceRequest $request, string|int $id): JsonResponse
    {
        try {

            $serviceVendor = Business::serviceVendor()->find($id);

            if (! $serviceVendor) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            $inputs = $request->validated();

            if (! Business::serviceVendor()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            return response()->updated(__('business::messages.vendor.service_assigned', ['vendor' => strtolower($serviceVendor->service_vendor_name ?? 'N/A')]));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
