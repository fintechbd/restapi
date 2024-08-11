<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'enabled' => $this->enabled,
            'vendor_code' => $this->vendor_code ?? (object) [],
            'city_data' => $this->city_data,
            'country_id' => $this->country_id ?? null,
            'country_name' => ($this->country != null) ? $this->country->name : null,
            'state_id' => $this->state_id ?? null,
            'state_name' => ($this->state != null) ? $this->state->name : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'restored_at' => $this->restored_at,
            ];
    }
}
