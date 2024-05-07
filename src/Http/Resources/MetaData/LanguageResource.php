<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
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
            'name' => $this->language['name'] ?? null,
            'code' => $this->language['code'] ?? null,
            'native' => $this->language['native'] ?? null,
            'logo_svg' => $this->getFirstMediaUrl('logo_svg'),
            'logo_png' => $this->getFirstMediaUrl('logo_png'),
            'enabled' => $this->country_data['language_enabled'] ?? false,
            'language_data' => $this->languages,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
