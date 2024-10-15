<?php

namespace Fintech\RestApi\Http\Controllers\Core;
use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Facades\Core;
use Fintech\RestApi\Http\Resources\Core\ClientErrorResource;
use Fintech\RestApi\Http\Resources\Core\ClientErrorCollection;
use Fintech\RestApi\Http\Requests\Core\ImportClientErrorRequest;
use Fintech\RestApi\Http\Requests\Core\StoreClientErrorRequest;
use Fintech\RestApi\Http\Requests\Core\UpdateClientErrorRequest;
use Fintech\RestApi\Http\Requests\Core\IndexClientErrorRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ClientErrorController
 * @package Fintech\RestApi\Http\Controllers\Core
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ClientError
 * @lrd:end
 *
 */

class ClientErrorController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ClientError* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexClientErrorRequest $request
     * @return ClientErrorCollection|JsonResponse
     */
    public function index(IndexClientErrorRequest $request): ClientErrorCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $clientErrorPaginate = Core::clientError()->list($inputs);

            return new ClientErrorCollection($clientErrorPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *ClientError* resource in storage.
     * @lrd:end
     *
     * @param StoreClientErrorRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StoreClientErrorRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $clientError = Core::clientError()->create($inputs);

            if (!$clientError) {
                throw (new StoreOperationException)->setModel(config('fintech.core.client_error_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Client Error']),
                'id' => $clientError->id
             ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *ClientError* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return ClientErrorResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ClientErrorResource|JsonResponse
    {
        try {

            $clientError = Core::clientError()->find($id);

            if (!$clientError) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.client_error_model'), $id);
            }

            return new ClientErrorResource($clientError);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *ClientError* resource using id.
     * @lrd:end
     *
     * @param UpdateClientErrorRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateClientErrorRequest $request, string|int $id): JsonResponse
    {
        try {

            $clientError = Core::clientError()->find($id);

            if (!$clientError) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.client_error_model'), $id);
            }

            $inputs = $request->validated();

            if (!Core::clientError()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.core.client_error_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Client Error']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ClientError* resource using id.
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws DeleteOperationException
     */
    public function destroy(string|int $id)
    {
        try {

            $clientError = Core::clientError()->find($id);

            if (!$clientError) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.client_error_model'), $id);
            }

            if (!Core::clientError()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.core.client_error_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Client Error']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ClientError* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $clientError = Core::clientError()->find($id, true);

            if (!$clientError) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.client_error_model'), $id);
            }

            if (!Core::clientError()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.core.client_error_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Client Error']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ClientError* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexClientErrorRequest $request
     * @return JsonResponse
     */
    public function export(IndexClientErrorRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $clientErrorPaginate = Core::clientError()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Client Error']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ClientError* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportClientErrorRequest $request
     * @return ClientErrorCollection|JsonResponse
     */
    public function import(ImportClientErrorRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $clientErrorPaginate = Core::clientError()->list($inputs);

            return new ClientErrorCollection($clientErrorPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
