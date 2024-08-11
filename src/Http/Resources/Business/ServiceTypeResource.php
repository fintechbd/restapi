<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $service_type_parent_id
 * @property mixed $serviceTypeParent
 * @property string $service_type_name
 * @property string $service_type_slug
 * @property string $service_type_is_parent
 * @property string $service_type_is_description
 * @property int $service_type_step
 * @property array $service_type_data
 * @property bool $enabled
 * @property mixed $all_parent_list
 * @property mixed $links
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @method getKey()
 * @method getFirstMediaUrl(string $string)
 */
class ServiceTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->getKey() ?? null,
            'service_type_parent_id' => $this->service_type_parent_id ?? null,
            'service_type_parent_name' => $this->serviceTypeParent->service_type_name ?? null,
            'service_type_parent_list' => $this->all_parent_list ?? null,
            'service_type_name' => $this->service_type_name ?? null,
            'service_type_slug' => $this->service_type_slug ?? null,
            'service_type_is_parent' => $this->service_type_is_parent ?? null,
            'service_type_is_description' => $this->service_type_is_description ?? null,
            'service_type_step' => $this->service_type_step ?? null,
            'service_type_data' => $this->service_type_data ?? null,
            'service_type_log_svg' => $this->getFirstMediaUrl('logo_svg') ?? null,
            'service_type_log_png' => $this->getFirstMediaUrl('logo_png') ?? null,
            'enabled' => $this->enabled ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return $data;
    }
}
