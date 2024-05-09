<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Auth\ImportRoleRequest;
use Fintech\RestApi\Http\Requests\Auth\IndexRoleRequest;
use Fintech\RestApi\Http\Requests\Auth\StoreRoleRequest;
use Fintech\RestApi\Http\Requests\Auth\UpdateRoleRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Auth\RoleCollection;
use Fintech\RestApi\Http\Resources\Auth\RoleResource;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Fintech\RestApi\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class RoleController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to role
 *
 * @lrd:end
 */
class RoleController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the role resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexRoleRequest $request): RoleCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $rolePaginate = Auth::role()->list($inputs);

            return new RoleCollection($rolePaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new role resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $role = Auth::role()->create($inputs);

            if (! $role) {
                throw (new StoreOperationException())->setModel(config('fintech.auth.role_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Role']),
                'id' => $role->getKey(),
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @LRDparam trashed boolean|nullable
     *
     * @lrd:start
     * Return a specified role resource found by id.
     *
     * @lrd:end
     */
    public function show(string|int $id): RoleResource|JsonResponse
    {
        try {

            $role = Auth::role()->find($id);

            if (! $role) {
                throw (new ModelNotFoundException())->setModel(config('fintech.auth.role_model'), $id);
            }

            return new RoleResource($role);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified role resource using id.
     *
     * @lrd:end
     */
    public function update(UpdateRoleRequest $request, string|int $id): JsonResponse
    {
        try {

            $role = Auth::role()->find($id);

            if (! $role) {
                throw (new ModelNotFoundException())->setModel(config('fintech.auth.role_model'), $id);
            }

            $inputs = $request->validated();

            if (! Auth::role()->update($id, $inputs)) {

                throw (new UpdateOperationException())->setModel(config('fintech.auth.role_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Role']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified role resource using id.
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

            $role = Auth::role()->find($id);

            if (! $role) {
                throw (new ModelNotFoundException())->setModel(config('fintech.auth.role_model'), $id);
            }

            if (! Auth::role()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.auth.role_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Role']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified role resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $role = Auth::role()->find($id, true);

            if (! $role) {
                throw (new ModelNotFoundException())->setModel(config('fintech.auth.role_model'), $id);
            }

            if (! Auth::role()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.auth.role_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'Role']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the role resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexRoleRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $rolePaginate = Auth::role()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Role']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the role resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportRoleRequest $request): RoleCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $rolePaginate = Auth::role()->list($inputs);

            return new RoleCollection($rolePaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $label = 'name';

            $attribute = 'id';

            if (! empty($filters['label'])) {
                $label = $filters['label'];
                unset($filters['label']);
            }

            if (! empty($filters['attribute'])) {
                $attribute = $filters['attribute'];
                unset($filters['attribute']);
            }

            $entries = Auth::role()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'attribute' => $entry->{$attribute} ?? 'id',
                    'label' => $entry->{$label} ?? 'name',
                ];
            })->toArray();

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return $this->failed($exception->getMessage());
        }
    }
}
