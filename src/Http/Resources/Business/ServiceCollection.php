<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
class ServiceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($service) {
            $data = [
                'id' => $service->getKey() ?? null,
                'service_type_id' => $service->service_type_id ?? null,
                'service_type_name' => isset($service->serviceType) ? $service->serviceType->service_type_name : null,
                'service_type_parent_name' => $service->serviceType->serviceTypeParent->service_type_name ?? null,
                'service_type_parent_list' => $service->serviceType->all_parent_list ?? null,
                'service_vendor_id' => $service->service_vendor_id ?? null,
                'service_vendor_name' => isset($service->serviceVendor) ? $service->serviceVendor->service_vendor_name : null,
                'service_name' => $service->service_name ?? null,
                'service_slug' => $service->service_slug ?? null,
                'service_notification' => $service->service_notification ?? null,
                'service_delay' => $service->service_delay ?? null,
                'service_stat_policy' => $service->service_stat_policy ?? null,
                'service_serial' => $service->service_serial ?? null,
                'service_data' => $service->service_data ?? null,
                'service_logo_svg' => $service->getFirstMediaUrl('logo_svg') ?? null,
                'service_logo_png' => $service->getFirstMediaUrl('logo_png') ?? null,
                'enabled' => $service->enabled ?? null,
                'links' => $service->links,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ];

            return $data;
        })->toArray();
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
