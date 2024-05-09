<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\RestApi\Http\Requests\Transaction\ImportUserAccountRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexUserAccountRequest;
use Fintech\RestApi\Http\Requests\Transaction\StoreUserAccountRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdateUserAccountRequest;
use Fintech\RestApi\Http\Resources\Transaction\UserAccountCollection;
use Fintech\RestApi\Http\Resources\Transaction\UserAccountResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class UserAccountController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to UserAccount
 *
 * @lrd:end
 */
class UserAccountController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *UserAccount* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexUserAccountRequest $request): UserAccountCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userAccountPaginate = Transaction::userAccount()->list($inputs);

            return new UserAccountCollection($userAccountPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *UserAccount* resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreUserAccountRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userAccount = Transaction::userAccount()->create($inputs);

            if (! $userAccount) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.user_account_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'User Account']),
                'id' => $userAccount->getKey(),
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *UserAccount* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): UserAccountResource|JsonResponse
    {
        try {

            $userAccount = Transaction::userAccount()->find($id);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return new UserAccountResource($userAccount);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *UserAccount* resource using id.
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

            $userAccount = Transaction::userAccount()->find($id);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            if (! Transaction::userAccount()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'User Account']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *UserAccount* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $userAccount = Transaction::userAccount()->find($id, true);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            if (! Transaction::userAccount()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'User Account']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *UserAccount* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexUserAccountRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userAccountPaginate = Transaction::userAccount()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'User Account']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *UserAccount* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return \Fintech\RestApi\Http\Resources\Transaction\UserAccountCollection|JsonResponse
     */
    public function import(ImportUserAccountRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userAccountPaginate = Transaction::userAccount()->list($inputs);

            return new UserAccountCollection($userAccountPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified user account active or inactive.
     *
     * @lrd:end
     *
     * N.B after toggle update actions follow
     *
     * @throws ModelNotFoundException
     *
     * @see \Fintech\MetaData\Observers\CountryObserver
     */
    public function toggle(string|int $id): JsonResponse
    {
        try {

            $userAccount = Transaction::userAccount()->find($id);

            if (! $userAccount) {
                throw (new ModelNotFoundException())->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            if (! Transaction::userAccount()->update($id, ['enabled' => ! $userAccount->enabled])) {
                throw (new UpdateOperationException())->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return $this->updated(__('metadata::messages.user_account.status_changed', ['status' => ($userAccount->enabled) ? 'Inactive' : 'Active']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *UserAccount* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateUserAccountRequest $request, string|int $id): JsonResponse
    {
        try {

            $userAccount = Transaction::userAccount()->find($id);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            $inputs = $request->validated();

            if (! Transaction::userAccount()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'User Account']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
