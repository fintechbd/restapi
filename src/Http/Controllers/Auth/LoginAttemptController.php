<?php

namespace Fintech\RestApi\Http\Controllers\Auth;
use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Auth\Facades\Auth;
use Fintech\RestApi\Http\Resources\Auth\LoginAttemptResource;
use Fintech\RestApi\Http\Resources\Auth\LoginAttemptCollection;
use Fintech\RestApi\Http\Requests\Auth\ImportLoginAttemptRequest;
use Fintech\RestApi\Http\Requests\Auth\IndexLoginAttemptRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class LoginAttemptController
 * @package Fintech\RestApi\Http\Controllers\Auth
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to LoginAttempt
 * @lrd:end
 *
 */

class LoginAttemptController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *LoginAttempt* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexLoginAttemptRequest $request
     * @return LoginAttemptCollection|JsonResponse
     */
    public function index(IndexLoginAttemptRequest $request): LoginAttemptCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $loginAttemptPaginate = Auth::loginAttempt()->list($inputs);

            return new LoginAttemptCollection($loginAttemptPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *LoginAttempt* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return LoginAttemptResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): LoginAttemptResource|JsonResponse
    {
        try {

            $loginAttempt = Auth::loginAttempt()->find($id);

            if (!$loginAttempt) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.login_attempt_model'), $id);
            }

            return new LoginAttemptResource($loginAttempt);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *LoginAttempt* resource using id.
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

            $loginAttempt = Auth::loginAttempt()->find($id);

            if (!$loginAttempt) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.login_attempt_model'), $id);
            }

            if (!Auth::loginAttempt()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.auth.login_attempt_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Login Attempt']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *LoginAttempt* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $loginAttempt = Auth::loginAttempt()->find($id, true);

            if (!$loginAttempt) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.login_attempt_model'), $id);
            }

            if (!Auth::loginAttempt()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.auth.login_attempt_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Login Attempt']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *LoginAttempt* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexLoginAttemptRequest $request
     * @return JsonResponse
     */
    public function export(IndexLoginAttemptRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $loginAttemptPaginate = Auth::loginAttempt()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Login Attempt']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *LoginAttempt* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportLoginAttemptRequest $request
     * @return LoginAttemptCollection|JsonResponse
     */
    public function import(ImportLoginAttemptRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $loginAttemptPaginate = Auth::loginAttempt()->list($inputs);

            return new LoginAttemptCollection($loginAttemptPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
