<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\RestApi\Http\Requests\Auth\CreateOneTimePinRequest;
use Fintech\RestApi\Http\Requests\Auth\VerifyOneTimePinRequest;
use Fintech\RestApi\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class OneTimePinController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * API let user verify using mobile, email and user account
     * field value can only between **email|mobile|user**
     * send verification link or otp as per configuration
     *
     * @lrd:end
     *
     * @throws Exception
     */
    public function request(CreateOneTimePinRequest $request): JsonResponse
    {
        $targetField = $request->has('mobile')
            ? 'mobile' :
            (
                $request->has('email')
                ? 'email' :
                ($request->has('user') ? 'user' : null)
            );

        $targetValue = $request->input($targetField);

        try {

            if (empty($targetValue)) {
                throw new \InvalidArgumentException('Input field must be one of (mobile, email, user) is not present or value is empty.');
            }

            $response = Auth::otp()->create($targetValue);

            if (! $response['status']) {
                throw new Exception($response['message']);
            }

            unset($response['status']);

            return $this->success($response);

        } catch (Exception $exception) {
            return $this->failed($exception->getMessage());
        }
    }

    /**
     * Send a new email verification notification.
     */
    public function verify(VerifyOneTimePinRequest $request): JsonResponse
    {
        $token = $request->input('token');

        try {

            if (! Auth::otp()->exists($token)) {
                throw new Exception(__('auth::messages.verify.invalid'));
            }

            return $this->success(__('auth::messages.verify.success'));

        } catch (Exception $exception) {
            return $this->failed($exception->getMessage());
        }
    }
}
