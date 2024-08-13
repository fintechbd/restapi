<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Carbon\Carbon;
use Fintech\Auth\Models\Profile;
use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property mixed $roles
 */

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
class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($user) {
            $data = [
                'id' => $user->getKey() ?? null,
                'parent_id' => $user->parent_id ?? null,
                'parent_name' => ($user->parent) ? $user->parent->name : null,
                'name' => $user->name ?? null,
                'mobile' => $user->mobile ?? null,
                'email' => $user->email ?? null,
                'login_id' => $user->login_id ?? null,
                'photo' => $user->getFirstMediaUrl('photo'),
                'status' => $user->status ?? null,
                'language' => $user->language ?? null,
                'currency' => $user->currency ?? null,
                'app_version' => $user->app_version ?? null,
                'roles' => ($user->roles) ? $user->roles->toArray() : [],
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
            unset($data['roles'][0]['links'], $data['roles'][0]['pivot'], $data['roles'][0]['permissions']);
            /**
             * @var Profile $profile
             */
            $profile = $user->profile;

            $profile_data = [
                'profile_data' => $profile->user_profile_data ?? (object) [],
                'id_type' => $profile->id_type ?? null,
                'id_no' => $profile->id_no ?? null,
                'id_issue_country' => $profile->id_issue_country ?? null,
                'id_expired_at' => $profile->id_expired_at ?? null,
                'id_issue_at' => $profile->id_issue_at ?? null,
                'id_no_duplicate' => $profile->id_no_duplicate ?? null,
                'date_of_birth' => $profile->date_of_birth ?? null,
                'present_country_id' => $profile->present_country_id ?? null,
                'present_country_name' => null,
                'blacklisted' => $profile->blacklisted ?? null,
                'proof_of_address' => $this->formatMediaCollection($profile?->getMedia('proof_of_address') ?? null),
                'ekyc' => $profile->user_profile_data['ekyc'] ?? (object) [],
            ];

            if (Core::packageExists('MetaData')) {
                $profile_data['present_country_name'] = $profile->presentCountry?->name ?? null;
            }

            return array_merge($data, $profile_data);

        })->toArray();
    }

    private function formatMediaCollection($collection = null): array
    {
        $data = [];

        if ($collection != null) {
            $collection->each(function (Media $media) use (&$data) {
                $data[$media->getCustomProperty('type')][$media->getCustomProperty('side')] = $media->getFullUrl();
            });
        }

        return $data;
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'options' => [
                'dir' => Constant::SORT_DIRECTIONS,
                'per_page' => Constant::PAGINATE_LENGTHS,
                'sort' => ['id', 'name', 'created_at', 'updated_at'],
            ],
            'query' => $request->all(),
        ];
    }
}
