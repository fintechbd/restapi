<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;
use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Transaction\Facades\Transaction;
use Fintech\RestApi\Http\Resources\Transaction\PolicyResource;
use Fintech\RestApi\Http\Resources\Transaction\PolicyCollection;
use Fintech\RestApi\Http\Requests\Transaction\ImportPolicyRequest;
use Fintech\RestApi\Http\Requests\Transaction\StorePolicyRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdatePolicyRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexPolicyRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class PolicyController
 * @package Fintech\RestApi\Http\Controllers\Transaction
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Policy
 * @lrd:end
 *
 */

class PolicyController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Policy* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexPolicyRequest $request
     * @return PolicyCollection|JsonResponse
     */
    public function index(IndexPolicyRequest $request): PolicyCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $policyPaginate = Transaction::policy()->list($inputs);

            return new PolicyCollection($policyPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Policy* resource in storage.
     * @lrd:end
     *
     * @param StorePolicyRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StorePolicyRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $policy = Transaction::policy()->create($inputs);

            if (!$policy) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.policy_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Policy']),
                'id' => $policy->id
             ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Policy* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return PolicyResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): PolicyResource|JsonResponse
    {
        try {

            $policy = Transaction::policy()->find($id);

            if (!$policy) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            return new PolicyResource($policy);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Policy* resource using id.
     * @lrd:end
     *
     * @param UpdatePolicyRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdatePolicyRequest $request, string|int $id): JsonResponse
    {
        try {

            $policy = Transaction::policy()->find($id);

            if (!$policy) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            $inputs = $request->validated();

            if (!Transaction::policy()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Policy']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Policy* resource using id.
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

            $policy = Transaction::policy()->find($id);

            if (!$policy) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            if (!Transaction::policy()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.policy_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Policy']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Policy* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $policy = Transaction::policy()->find($id, true);

            if (!$policy) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            if (!Transaction::policy()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.policy_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Policy']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Policy* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexPolicyRequest $request
     * @return JsonResponse
     */
    public function export(IndexPolicyRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $policyPaginate = Transaction::policy()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Policy']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Policy* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportPolicyRequest $request
     * @return PolicyCollection|JsonResponse
     */
    public function import(ImportPolicyRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $policyPaginate = Transaction::policy()->list($inputs);

            return new PolicyCollection($policyPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
