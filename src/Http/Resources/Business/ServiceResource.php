<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int $service_type_id
 * @property string $service_type_name
 * @property int $service_vendor_id
 * @property string $service_vendor_name
 * @property string $service_name
 * @property string $service_slug
 * @property string $service_notification
 * @property string $service_delay
 * @property string $service_stat_policy
 * @property string $service_serial
 * @property array $service_data
 * @property mixed $links
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @method getKey()
 * @method getFirstMediaUrl(string $string)
 */
class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->getKey() ?? null,
            'service_type_id' => $this->service_type_id ?? null,
            'service_type_name' => isset($this->serviceType) ? $this->serviceType->service_type_name : null,
            'service_type_parent_name' => $this->serviceType->serviceTypeParent->service_type_name ?? null,
            'service_type_parent_list' => $this->serviceType->all_parent_list ?? null,
            'service_vendor_id' => $this->service_vendor_id ?? null,
            'service_vendor_name' => isset($this->serviceVendor) ? $this->serviceVendor->service_vendor_name : null,
            'service_name' => $this->service_name ?? null,
            'service_slug' => $this->service_slug ?? null,
            'service_notification' => $this->service_notification ?? null,
            'service_delay' => $this->service_delay ?? null,
            'service_stat_policy' => $this->service_stat_policy ?? null,
            'service_serial' => $this->service_serial ?? null,
            'service_data' => $this->service_data ?? null,
            'service_logo_svg' => $this->getFirstMediaUrl('logo_svg') ?? null,
            'service_logo_png' => $this->getFirstMediaUrl('logo_png') ?? null,
            'enabled' => $this->enabled ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return $data;
    }
}
