<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CreateOneTimePinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'mobile' => 'required_without_all:email,user|string|min:10|max:15',
            'email' => 'required_without_all:mobile,user|string|email:rfc,dns',
            'user' => 'required_without_all:mobile,email|integer|min:1',
        ];
    }

    /**
     * clear the rate limiter if authenticated
     */
    public function clearRateLimited(): void
    {
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $key = $this->input('mobile', $this->input('email', $this->input('user')));

        return Str::transliterate(Str::lower($key.'|'.$this->ip()));
    }

    /**
     * count the rate limiter if authenticated
     */
    public function hitRateLimited(): void
    {
        RateLimiter::hit($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        abort(Response::HTTP_TOO_MANY_REQUESTS, trans('auth::messages.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]));

    }
}
