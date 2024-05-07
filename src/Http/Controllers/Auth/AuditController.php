<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\RestApi\Http\Requests\Auth\IndexAuditRequest;
use Fintech\RestApi\Http\Resources\Auth\AuditCollection;
use Fintech\RestApi\Http\Resources\Auth\AuditResource;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class AuditController
 * @package Fintech\RestApi\Http\Controllers\Auth
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Audit
 * @lrd:end
 *
 */
class AuditController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *Audit* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexAuditRequest $request
     * @return AuditCollection|JsonResponse
     */
    public function index(IndexAuditRequest $request): AuditCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $auditPaginate = Auth::audit()->list($inputs);

            return new AuditCollection($auditPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *Audit* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return AuditResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): AuditResource|JsonResponse
    {
        try {

            $audit = Auth::audit()->find($id);

            if (!$audit) {
                throw (new ModelNotFoundException())->setModel(config('fintech.auth.audit_model'), $id);
            }

            return new AuditResource($audit);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Audit* resource using id.
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

            $audit = Auth::audit()->find($id);

            if (!$audit) {
                throw (new ModelNotFoundException())->setModel(config('fintech.auth.audit_model'), $id);
            }

            if (!Auth::audit()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.auth.audit_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Audit']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
