<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Carbon\Carbon;
use Fintech\Auth\Models\Profile;
use Fintech\Core\Facades\Core;
use Fintech\RestApi\Traits\IdDocTypeResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class UserResource
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
 * @property-read float $total_balance
 * @property-read Collection $roles
 * @property-read Profile|null $profile
 * @property-read Carbon $email_verified_at
 * @property-read Carbon $mobile_verified_at
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property mixed $parent
 * @property mixed $parent_id
 * @property mixed $links
 *
 * @method getKey()
 * @method getFirstMediaUrl(string $string)
 */
class UserResource extends JsonResource
{
    use IdDocTypeResourceTrait;

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->getKey() ?? null,
            'parent_id' => $this->parent_id ?? null,
            'parent_name' => ($this->parent) ? $this->parent->name : null,
            'name' => $this->name ?? null,
            'mobile' => $this->mobile ?? null,
            'email' => $this->email ?? null,
            'login_id' => $this->login_id ?? null,
            'photo' => $this->getFirstMediaUrl('photo'),
            'status' => $this->status ?? null,
            'language' => $this->language ?? null,
            'currency' => $this->currency ?? null,
            'app_version' => $this->app_version ?? null,
            'roles' => ($this->roles) ? $this->roles->toArray() : [],
            'balances' => ($this->userAccounts)
                ? $this->userAccounts->pluck('user_account_data')->toArray()
                : [],
            'profile_data' => [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        unset($data['roles'][0]['links'], $data['roles'][0]['pivot'], $data['roles'][0]['permissions']);

        /**
         * @var Profile $profile
         */
        $profile = $this->profile;

        $profile_data = [
            'profile_data' => $profile->user_profile_data ?? null,
            'id_type' => $profile->id_type ?? null,
            'id_no' => $profile->id_no ?? null,
            'id_issue_country' => $profile->id_issue_country ?? null,
            'id_expired_at' => $profile->id_expired_at ?? null,
            'id_issue_at' => $profile->id_issue_at ?? null,
            'id_no_duplicate' => $profile->id_no_duplicate ?? null,
            'documents' => $this->formatMediaCollection($profile?->getMedia('documents') ?? null),
            'date_of_birth' => $profile->date_of_birth ?? null,
            'permanent_address' => $profile->permanent_address ?? null,
            'permanent_city_id' => $profile->permanent_city_id ?? null,
            'permanent_city_name' => null,
            'permanent_state_id' => $profile->permanent_state_id ?? null,
            'permanent_state_name' => null,
            'permanent_country_id' => $profile->permanent_country_id ?? null,
            'permanent_country_name' => null,
            'permanent_post_code' => $profile->permanent_post_code ?? null,

            'present_address' => $profile->present_address ?? null,
            'present_city_id' => $profile->present_city_id ?? null,
            'present_city_name' => null,
            'present_state_id' => $profile->present_state_id ?? null,
            'present_state_name' => null,
            'present_country_id' => $profile->present_country_id ?? null,
            'present_country_name' => null,
            'present_post_code' => $profile->present_post_code ?? null,
            'blacklisted' => $profile->blacklisted ?? null,
            'proof_of_address' => $this->formatMediaCollection($profile->getMedia('proof_of_address')),
            'ekyc' => $profile->user_profile_data['ekyc'] ?? (object) [],
        ];

        if (Core::packageExists('MetaData')) {
            $profile_data['permanent_city_name'] = $profile->permanentCity?->name ?? null;
            $profile_data['permanent_state_name'] = $profile->permanentState?->name ?? null;
            $profile_data['permanent_country_name'] = $profile->permanentCountry?->name ?? null;
            $profile_data['present_city_name'] = $profile->presentCity?->name ?? null;
            $profile_data['present_state_name'] = $profile->presentState?->name ?? null;
            $profile_data['present_country_name'] = $profile->presentCountry?->name ?? null;
        }

        return array_merge($data, $profile_data);
    }
}
