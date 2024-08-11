<?php

namespace Fintech\RestApi\Http\Resources\Banco;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $user_id
 * @property int $city_id
 * @property string $city
 * @property int $state_id
 * @property string $state
 * @property int $country_id
 * @property int $beneficiary_type_id
 * @property string $country
 * @property Collection|array $beneficiaryType
 * @property int $relation_id
 * @property string $relation
 * @property string $beneficiary_name
 * @property string $beneficiary_mobile
 * @property string $beneficiary_address
 * @property string $beneficiary_data
 * @property bool $enabled
 * @property mixed $links
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @method getKey()
 * @method getFirstMediaUrl(string $string)
 */
class BeneficiaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey() ?? null,
            'user_id' => $this->user_id ?? null,
            'user_name' => $this->user->name ?? null,
            'city_id' => $this->city_id ?? null,
            'city_name' => $this->city->name ?? null,
            'state_id' => $this->state_id ?? null,
            'state_name' => $this->state->name ?? null,
            'country_id' => $this->country_id ?? null,
            'country_name' => $this->country->name ?? null,
            'beneficiary_type_id' => $this->beneficiary_type_id ?? null,
            'beneficiary_type_name' => $this->beneficiaryType?->beneficiary_type_name ?? null,
            'relation_id' => $this->relation_id ?? null,
            'relation' => $this->relation->name ?? null,
            'beneficiary_name' => $this->beneficiary_name ?? null,
            'beneficiary_photo' => $this->getFirstMediaUrl('photo') ?? null,
            'beneficiary_mobile' => $this->beneficiary_mobile ?? null,
            'beneficiary_address' => $this->beneficiary_address ?? null,
            'beneficiary_data' => $this->beneficiary_data ?? null,
            'enabled' => $this->enabled ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
