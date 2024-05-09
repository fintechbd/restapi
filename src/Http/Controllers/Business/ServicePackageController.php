<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ImportServicePackageRequest;
use Fintech\RestApi\Http\Requests\Business\IndexServicePackageRequest;
use Fintech\RestApi\Http\Requests\Business\StoreServicePackageRequest;
use Fintech\RestApi\Http\Requests\Business\UpdateServicePackageRequest;
use Fintech\RestApi\Http\Resources\Business\ServicePackageCollection;
use Fintech\RestApi\Http\Resources\Business\ServicePackageResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ServicePackageController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ServicePackage
 *
 * @lrd:end
 */
class ServicePackageController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ServicePackage* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexServicePackageRequest $request): ServicePackageCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $servicePackagePaginate = Business::servicePackage()->list($inputs);

            return new ServicePackageCollection($servicePackagePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ServicePackage* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreServicePackageRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $servicePackage = Business::servicePackage()->create($inputs);

            if (!$servicePackage) {
                throw (new StoreOperationException)->setModel(config('fintech.business.service_package_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Service Package']),
                'id' => $servicePackage->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ServicePackage* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ServicePackageResource|JsonResponse
    {
        try {

            $servicePackage = Business::servicePackage()->find($id);

            if (!$servicePackage) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_package_model'), $id);
            }

            return new ServicePackageResource($servicePackage);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ServicePackage* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateServicePackageRequest $request, string|int $id): JsonResponse
    {
        try {

            $servicePackage = Business::servicePackage()->find($id);

            if (!$servicePackage) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_package_model'), $id);
            }

            $inputs = $request->validated();

            if (!Business::servicePackage()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.service_package_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Service Package']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ServicePackage* resource using id.
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

            $servicePackage = Business::servicePackage()->find($id);

            if (!$servicePackage) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_package_model'), $id);
            }

            if (!Business::servicePackage()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.business.service_package_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Service Package']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ServicePackage* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $servicePackage = Business::servicePackage()->find($id, true);

            if (!$servicePackage) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_package_model'), $id);
            }

            if (!Business::servicePackage()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.business.service_package_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'Service Package']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ServicePackage* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexServicePackageRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $servicePackagePaginate = Business::servicePackage()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Service Package']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ServicePackage* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ServicePackageCollection|JsonResponse
     */
    public function import(ImportServicePackageRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $servicePackagePaginate = Business::servicePackage()->list($inputs);

            return new ServicePackageCollection($servicePackagePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
