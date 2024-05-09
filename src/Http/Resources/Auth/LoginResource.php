<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Carbon\Carbon;
use Fintech\Auth\Models\Profile;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;
use stdClass;

/**
 * Class LoginResource
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $mobile
 * @property-read string $email
 * @property-read string $login_id
 * @property-read string $status
 * @property-read string $language
 * @property-read string $currency
 * @property-read string $app_version
 * @property-read HasMany $userAccounts
 * @property-read float $total_balance
 * @property-read Collection $roles
 * @property-read Profile|null $profile
 * @property-read Carbon $email_verified_at
 * @property-read Carbon $mobile_verified_at
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $logged_in_at
 *
 * @method Collection getAllPermissions()
 * @method Collection getFirstMediaUrl(string $collection)
 * @method NewAccessToken createToken(string $origin)
 */
class LoginResource extends JsonResource
{
    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        $origin = Str::slug(config('app.name'));

        $permissions = [];

        $permissionCollection = $this->getAllPermissions();

        if (! $permissionCollection->isEmpty()) {
            $permissions = $permissionCollection->pluck('name')->toArray();
        }

        return [
            'access' => [
                'token' => $this->createToken($origin)->plainTextToken,
                'type' => 'bearer',
                'permissions' => $permissions,
            ],
            'message' => trans('auth::messages.success'),
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request)
    {
        $this->resource->load(['profile', 'roles']);

        $return = [
            'id' => $this->getKey(),
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'login_id' => $this->login_id,
            'status' => $this->status,
            'language' => $this->language,
            'currency' => $this->currency,
            'app_version' => $this->app_version,
            'photo' => $this->getFirstMediaUrl('photo'),
            'role_id' => null,
            'role_name' => null,
            'profile' => (($this->profile != null)
                ? (new ProfileResource($this->profile))
                : (new stdClass())),
            'balances' => ($this->userAccounts)
                ? $this->userAccounts->pluck('user_account_data')->toArray()
                : [],
            'last_logged_at' => $this->logged_in_at,
            'email_verified_at' => $this->email_verified_at,
            'mobile_verified_at' => $this->mobile_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->roles != null) {
            $role = $this->roles->first();
            $return['role_id'] = $role->id ?? null;
            $return['role_name'] = $role->name ?? null;
        }

        return $return;
    }
}
