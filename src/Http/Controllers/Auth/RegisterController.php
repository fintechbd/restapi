<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\RestApi\Http\Requests\Auth\RegistrationRequest;
use Fintech\Core\Traits\ApiResponseTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class RegisterController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle an incoming registration request.
     * @param RegistrationRequest $request
     * @return JsonResponse
     */
    public function __invoke(RegistrationRequest $request): JsonResponse
    {
        $userFields = [
            'name', 'mobile', 'email', 'login_id', 'password', 'pin',
            'language', 'currency', 'app_version', 'fcm_token', 'photo'
        ];

        try {

            $user = Auth::user()->create($request->only($userFields));

            $profile = Auth::profile()->create($user->getKey(), $request->except($userFields));

            event(new Registered($user));

            return $this->created('Registration Successful.');

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
