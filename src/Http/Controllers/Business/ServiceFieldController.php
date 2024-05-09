<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ImportServiceFieldRequest;
use Fintech\RestApi\Http\Requests\Business\IndexServiceFieldRequest;
use Fintech\RestApi\Http\Requests\Business\StoreServiceFieldRequest;
use Fintech\RestApi\Http\Requests\Business\UpdateServiceFieldRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceFieldCollection;
use Fintech\RestApi\Http\Resources\Business\ServiceFieldResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ServiceFieldController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ServiceField
 *
 * @lrd:end
 */
class ServiceFieldController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ServiceField* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexServiceFieldRequest $request): ServiceFieldCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceFieldPaginate = Business::serviceField()->list($inputs);

            return new ServiceFieldCollection($serviceFieldPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ServiceField* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreServiceFieldRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceField = Business::serviceField()->create($inputs);

            if (! $serviceField) {
                throw (new StoreOperationException)->setModel(config('fintech.business.service_field_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Service Field']),
                'id' => $serviceField->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ServiceField* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ServiceFieldResource|JsonResponse
    {
        try {

            $serviceField = Business::serviceField()->find($id);

            if (! $serviceField) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_field_model'), $id);
            }

            return new ServiceFieldResource($serviceField);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ServiceField* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateServiceFieldRequest $request, string|int $id): JsonResponse
    {
        try {

            $serviceField = Business::serviceField()->find($id);

            if (! $serviceField) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_field_model'), $id);
            }

            $inputs = $request->validated();

            if (! Business::serviceField()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.service_field_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Service Field']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ServiceField* resource using id.
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

            $serviceField = Business::serviceField()->find($id);

            if (! $serviceField) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_field_model'), $id);
            }

            if (! Business::serviceField()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.business.service_field_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Service Field']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ServiceField* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $serviceField = Business::serviceField()->find($id, true);

            if (! $serviceField) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_field_model'), $id);
            }

            if (! Business::serviceField()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.business.service_field_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Service Field']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ServiceField* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexServiceFieldRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceFieldPaginate = Business::serviceField()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Service Field']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ServiceField* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ServiceFieldCollection|JsonResponse
     */
    public function import(ImportServiceFieldRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceFieldPaginate = Business::serviceField()->list($inputs);

            return new ServiceFieldCollection($serviceFieldPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
