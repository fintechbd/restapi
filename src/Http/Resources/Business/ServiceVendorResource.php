<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $service_vendor_name
 * @property string $service_vendor_slug
 * @property array $service_vendor_data
 * @property bool $enabled
 * @property mixed $links
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @method getKey()
 * @method getFirstMediaUrl(string $string)
 */
class ServiceVendorResource extends JsonResource
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
            'id' => $this->getKey() ?? null,
            'service_vendor_name' => $this->service_vendor_name ?? null,
            'service_vendor_slug' => $this->service_vendor_slug ?? null,
            'service_vendor_data' => $this->service_vendor_data ?? null,
            'service_vendor_logo_svg' => $this->getFirstMediaUrl('logo_svg') ?? null,
            'service_vendor_logo_png' => $this->getFirstMediaUrl('logo_png') ?? null,
            'enabled' => $this->enabled ?? null,
            'links' => $this->links,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
