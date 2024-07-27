<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Auth\ImportPermissionRequest;
use Fintech\RestApi\Http\Requests\Auth\IndexPermissionRequest;
use Fintech\RestApi\Http\Requests\Auth\StorePermissionRequest;
use Fintech\RestApi\Http\Requests\Auth\UpdatePermissionRequest;
use Fintech\RestApi\Http\Resources\Auth\PermissionCollection;
use Fintech\RestApi\Http\Resources\Auth\PermissionResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class PermissionController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to permission
 *
 * @lrd:end
 */
class PermissionController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the permission resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexPermissionRequest $request): PermissionCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $permissionPaginate = Auth::permission()->list($inputs);

            return new PermissionCollection($permissionPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new permission resource in storage.
     *
     * @lrd:end
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $permission = Auth::permission()->create($inputs);

            if (! $permission) {
                throw (new StoreOperationException)->setModel(config('fintech.auth.permission_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Permission']),
                'id' => $permission->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @LRDparam trashed boolean|nullable
     *
     * @lrd:start
     * Return a specified permission resource found by id.
     *
     * @lrd:end
     */
    public function show(string|int $id): PermissionResource|JsonResponse
    {
        try {

            $permission = Auth::permission()->find($id);

            if (! $permission) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.permission_model'), $id);
            }

            return new PermissionResource($permission);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified permission resource using id.
     *
     * @lrd:end
     */
    public function update(UpdatePermissionRequest $request, string|int $id): JsonResponse
    {
        try {

            $permission = Auth::permission()->find($id);

            if (! $permission) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.permission_model'), $id);
            }

            $inputs = $request->validated();

            if (! Auth::permission()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.auth.permission_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Permission']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified permission resource using id.
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

            $permission = Auth::permission()->find($id);

            if (! $permission) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.permission_model'), $id);
            }

            if (! Auth::permission()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.auth.permission_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Permission']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified permission resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $permission = Auth::permission()->find($id, true);

            if (! $permission) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.permission_model'), $id);
            }

            if (! Auth::permission()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.auth.permission_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Permission']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the permission resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexPermissionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $permissionPaginate = Auth::permission()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Permission']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the permission resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportPermissionRequest $request): PermissionCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $permissionPaginate = Auth::permission()->list($inputs);

            return new PermissionCollection($permissionPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
