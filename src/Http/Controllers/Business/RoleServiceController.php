<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\RoleServiceRequest;
use Fintech\RestApi\Http\Resources\Business\RoleServiceResource;
use Fintech\RestApi\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class RoleServiceController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * return services to a specified role resource using id.
     *
     * @lrd:end
     */
    public function show(string|int $id): RoleServiceResource|JsonResponse
    {
        try {

            $role = Auth::role()->find($id);

            if (! $role) {
                throw (new ModelNotFoundException())->setModel(config('fintech.auth.role_model'), $id);
            }

            return new RoleServiceResource($role);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Assign services to a specified role resource using id.
     *
     * @lrd:end
     */
    public function update(RoleServiceRequest $request, string|int $id): JsonResponse
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

            return $this->updated(__('business::messages.role.service_assigned', ['role' => strtolower($role->name ?? 'N/A')]));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
