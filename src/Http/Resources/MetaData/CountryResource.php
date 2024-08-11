<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method getKey()
 * @method getFirstMediaUrl(string $string)
 *
 * @property mixed $name
 * @property mixed $iso3
 * @property mixed $iso2
 * @property mixed $phone_code
 * @property mixed $capital
 * @property mixed $currency
 * @property mixed $currency_name
 * @property mixed $currency_symbol
 * @property mixed $nationality
 * @property mixed $timezones
 * @property mixed $country_data
 * @property mixed $latitude
 * @property mixed $longitude
 * @property mixed $emoji
 * @property mixed $enabled
 * @property mixed $region_id
 * @property mixed $region
 * @property mixed $subregion_id
 * @property mixed $subregion
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $deleted_at
 * @property mixed $restored_at
 * @property mixed $links
 */
class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'iso3' => $this->iso3,
            'iso2' => $this->iso2,
            'phone_code' => $this->phone_code,
            'capital' => $this->capital,
            'currency' => $this->currency,
            'currency_name' => $this->currency_name,
            'currency_symbol' => $this->currency_symbol,
            'nationality' => $this->nationality,
            'timezones' => $this->timezones,
            'vendor_code' => $this->vendor_code ?? (object) [],
            'country_data' => $this->country_data,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'emoji' => $this->emoji,
            'enabled' => $this->enabled,
            'logo_svg' => $this->getFirstMediaUrl('logo_svg'),
            'logo_png' => $this->getFirstMediaUrl('logo_png'),
            'region_id' => $this->region_id ?? null,
            'region_name' => ($this->region != null) ? $this->region->name : null,
            'subregion_id' => $this->subregion_id ?? null,
            'subregion_name' => ($this->subregion != null) ? $this->subregion->name : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'restored_at' => $this->restored_at,
            ];
    }
}
