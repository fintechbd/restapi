<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function currency;

class ServicePackageResource extends JsonResource
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
            'id' => $this?->getKey() ?? null,
            'service_id' => $this->service_id ?? null,
            'service_name' => $this->service?->service_name ?? null,
            'service_logo_svg' => $this?->service?->getFirstMediaUrl('logo_svg') ?? null,
            'service_logo_png' => $this?->service?->getFirstMediaUrl('logo_png') ?? null,
            'country_id' => $this->country_id ?? null,
            'country_name' => $this->country?->name ?? null,
            'country_currency' => $this->country?->currency ?? null,
            'name' => $this->name ?? null,
            'slug' => $this->slug ?? null,
            'description' => $this->description ?? null,
            'amount' => $this->amount ?? null,
            'amount_formatted' => (property_exists($this, 'amount'))
                ? (string) currency($this->amount ?? null, $this->country?->currency ?? null)
                : 'N/A',
            'enabled' => $this->enabled ?? null,
            'type' => $this->type ?? null,
            'service_package_data' => $this->service_package_data ?? (object) [],
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];
    }
}
