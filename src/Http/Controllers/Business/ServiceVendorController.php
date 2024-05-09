<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ImportServiceVendorRequest;
use Fintech\RestApi\Http\Requests\Business\IndexServiceVendorRequest;
use Fintech\RestApi\Http\Requests\Business\StoreServiceVendorRequest;
use Fintech\RestApi\Http\Requests\Business\UpdateServiceVendorRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceVendorCollection;
use Fintech\RestApi\Http\Resources\Business\ServiceVendorResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ServiceVendorController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ServiceVendor
 *
 * @lrd:end
 */
class ServiceVendorController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ServiceVendor* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexServiceVendorRequest $request): ServiceVendorCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceVendorPaginate = Business::serviceVendor()->list($inputs);

            return new ServiceVendorCollection($serviceVendorPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ServiceVendor* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreServiceVendorRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceVendor = Business::serviceVendor()->create($inputs);

            if (!$serviceVendor) {
                throw (new StoreOperationException)->setModel(config('fintech.business.service_vendor_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Service Vendor']),
                'id' => $serviceVendor->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ServiceVendor* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ServiceVendorResource|JsonResponse
    {
        try {

            $serviceVendor = Business::serviceVendor()->find($id);

            if (!$serviceVendor) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            return new ServiceVendorResource($serviceVendor);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ServiceVendor* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateServiceVendorRequest $request, string|int $id): JsonResponse
    {
        try {

            $serviceVendor = Business::serviceVendor()->find($id);

            if (!$serviceVendor) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            $inputs = $request->validated();

            if (!Business::serviceVendor()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Service Vendor']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ServiceVendor* resource using id.
     *
     * @lrd:end
     *
     * @return JsonResponse
     *
     * @throws ModelNotFoundException
     * @throws DeleteOperationException
     */
    public function destroy(string|int $id)
    {
        try {

            $serviceVendor = Business::serviceVendor()->find($id);

            if (!$serviceVendor) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            if (!Business::serviceVendor()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Service Vendor']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ServiceVendor* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $serviceVendor = Business::serviceVendor()->find($id, true);

            if (!$serviceVendor) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            if (!Business::serviceVendor()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.business.service_vendor_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Service Vendor']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ServiceVendor* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexServiceVendorRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceVendorPaginate = Business::serviceVendor()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Service Vendor']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ServiceVendor* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ServiceVendorCollection|JsonResponse
     */
    public function import(ImportServiceVendorRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceVendorPaginate = Business::serviceVendor()->list($inputs);

            return new ServiceVendorCollection($serviceVendorPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
