<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Core\Enums\Auth\UserStatus;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Auth\ImportUserRequest;
use Fintech\RestApi\Http\Requests\Auth\IndexUserRequest;
use Fintech\RestApi\Http\Requests\Auth\StoreUserRequest;
use Fintech\RestApi\Http\Requests\Auth\UpdateUserRequest;
use Fintech\RestApi\Http\Requests\Auth\UserAuthResetRequest;
use Fintech\RestApi\Http\Requests\Auth\UserStatusChangeRequest;
use Fintech\RestApi\Http\Requests\Auth\UserVerificationRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Auth\UserCollection;
use Fintech\RestApi\Http\Resources\Auth\UserResource;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;

/**
 * Class UserController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to user
 *
 * @lrd:end
 */
class UserController extends Controller
{
    private array $userFields = [
        'name', 'mobile', 'email', 'login_id', 'password', 'pin',
        'language', 'currency', 'app_version', 'fcm_token', 'photo',
        'roles', 'parent_id',
    ];

    /**
     * @lrd:start
     * Return a listing of the user resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexUserRequest $request): UserCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userPaginate = Auth::user()->list($inputs);

            return new UserCollection($userPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new user resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {

            $user = Auth::user()->create($request->only($this->userFields));

            if (! $user) {
                throw (new StoreOperationException)->setModel(config('fintech.auth.user_model'));
            }

            $profile = Auth::profile()->create($user->getKey(), $request->except($this->userFields));

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'User']),
                'id' => $user->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @LRDparam trashed boolean|nullable
     *
     * @lrd:start
     * Return a specified user resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): UserResource|JsonResponse
    {
        try {

            $user = Auth::user()->find($id);

            if (! $user) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.user_model'), $id);
            }

            return new UserResource($user);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified user resource using id.
     *
     * @lrd:end
     *
     * @throws UpdateOperationException
     */
    public function update(UpdateUserRequest $request, string|int $id): JsonResponse
    {

        try {

            $user = Auth::user()->find($id);

            if (! $user) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.user_model'), $id);
            }

            if (! Auth::user()->update($id, $request->only($this->userFields)) ||
                ! Auth::profile()->update($user->getKey(), $request->except($this->userFields))) {

                throw (new UpdateOperationException)->setModel(config('fintech.auth.user_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'User']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified user resource using id.
     *
     * @lrd:end
     *
     * @return JsonResponse
     *
     * @throws DeleteOperationException
     */
    public function destroy(string|int $id)
    {
        try {

            $user = Auth::user()->find($id);

            if (! $user) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.user_model'), $id);
            }

            if (! Auth::user()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.auth.user_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'User']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified user resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $user = Auth::user()->find($id, true);

            if (! $user) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.user_model'), $id);
            }

            if (! Auth::user()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.auth.user_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'User']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the user resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexUserRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userPaginate = Auth::user()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'User']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the user resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportUserRequest $request): UserCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userPaginate = Auth::user()->list($inputs);

            return new UserCollection($userPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Reset user pin, password or both from admin panel
     * and send an updated value to targeted user
     * system will also verify which user is requesting
     *
     * @lrd:end
     */
    public function reset(string|int $id, string $field, UserAuthResetRequest $request): JsonResponse
    {

        $requestUser = $request->user();

        try {

            $user = Auth::user()->find($id);

            if (! $user) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.user_model'), $id);
            }

            $response = Auth::user()->reset($user, $field);

            if (! $response['status']) {
                throw new Exception($response['response']);
            }

            return response()->success($response['message']);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Verification of user mobile, email, login_id
     *  if already exist then return false
     *
     * @lrd:end
     */
    public function verification(UserVerificationRequest $request): JsonResponse
    {

        $targetField = $request->has('mobile')
            ? 'mobile' :
            (
                $request->has('email')
                    ? 'email' :
                    ($request->has('login_id') ? 'login_id' : null)
            );

        $targetValue = $request->input($targetField);

        try {

            if (empty($targetValue)) {
                throw new InvalidArgumentException('Input field must be one of (mobile, email, login_id) is not present or value is empty.');
            }

            $userExists = Auth::user()->list([$targetField => $targetValue])->first();

            $response = [
                'data' => [
                    'valid' => $userExists == null,
                ],
                'message' => ($userExists == null)
                    ? "This is a valid user {$targetField}."
                    : "User already exist with this {$targetField}.",
                'query' => $request->all(),
            ];

            return response()->success($response);

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Change User Status from dropdown values
     *
     * @lrd:end
     */
    public function changeStatus(UserStatusChangeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();
            $user = Auth::user()->find($inputs['user_id']);

            if (! $user) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.user_model'), $inputs['user_id']);
            }

            $response = Auth::user()->updateRaw($user->getKey(), ['status' => $inputs['status']]);

            if (! $response) {
                throw (new UpdateOperationException)->setModel(config('fintech.auth.user_model'), $inputs['user_id']);
            }

            return response()->updated(__('auth::messages.user.status-change', ['status' => $inputs['status']]));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
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

            $entries = Auth::user()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'label' => $entry->{$label} ?? 'name',
                    'attribute' => $entry->{$attribute} ?? 'id',
                ];
            });

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }

    public function statusDropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $entries = collect();

            foreach (UserStatus::toArray() as $key => $status) {
                $entries->push(['label' => $status, 'attribute' => $key]);
            }

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }
}
