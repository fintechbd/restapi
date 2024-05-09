<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use ErrorException;
use Exception;
use Fintech\Auth\Events\PasswordResetRequested;
use Fintech\Auth\Events\PasswordResetSuccessful;
use Fintech\Auth\Facades\Auth;
use Fintech\Auth\Traits\GuessAuthFieldTrait;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Auth\ForgotPasswordRequest;
use Fintech\RestApi\Http\Requests\Auth\PasswordResetRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PasswordResetController extends Controller
{
    use GuessAuthFieldTrait;

    /**
     * @lrd:start
     * This api receive `login_id` as unique user then as per configuration
     * and send temporary password or reset link or One Time Pin verifcation
     * to proceed
     *
     * @lrd:end
     *
     * @throws Exception
     */
    public function store(ForgotPasswordRequest $request): JsonResponse
    {
        try {

            $attemptUser = Auth::user()->list($this->getAuthFieldFromInput($request));

            if ($attemptUser->isEmpty()) {
                throw new Exception(__('auth::messages.failed'));
            }

            $response = Auth::passwordReset()->notifyUser($attemptUser->first());

            if (! $response['status']) {
                throw new Exception($response['message']);
            }

            event(new PasswordResetRequested($attemptUser));

            return response()->success($response['message']);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @LRDparam password_confirmation string|required|min:8
     *
     * @lrd:start
     * This api receive `token`, `password` & `password_confirmation` to reset
     * user with given password. If otp or token didn't match throws exception
     * to proceed
     *
     * @lrd:end
     */
    public function update(PasswordResetRequest $request): JsonResponse
    {
        $passwordField = config('fintech.auth.password_field', 'password');

        $token = $request->input('token');

        $password = $request->input($passwordField);

        try {

            $activeToken = Auth::passwordReset()->verifyToken($token);

            $subRequest = app()->make(Request::class, [config('fintech.auth.auth_field', 'login_id') => $activeToken->email]);

            $targetedUser = Auth::user()->list($this->getAuthFieldFromInput($subRequest));

            if ($targetedUser->isEmpty()) {
                throw new ErrorException(__('auth::messages.reset.user_not_found'));
            }

            $targetedUser = $targetedUser->first();

            if (! Auth::user()->update($targetedUser->getKey(), [$passwordField => $password])) {
                throw (new UpdateOperationException())->setModel(config('fintech.auth.user_model'), $targetedUser->getKey());
            }

            event(new PasswordResetSuccessful($targetedUser));

            return response()->updated(__('auth::messages.reset.success'));

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }
}
