<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'country_id' => $this->getKey(),
            'country_name' => $this->name ?? null,
            'name' => $this->currency_name ?? null,
            'code' => $this->currency ?? null,
            'symbol' => $this->currency_symbol ?? null,
            'logo_svg' => $this->getFirstMediaUrl('logo_svg'),
            'logo_png' => $this->getFirstMediaUrl('logo_png'),
            'enabled' => $this->country_data['multi_currency_enabled'] ?? false,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
