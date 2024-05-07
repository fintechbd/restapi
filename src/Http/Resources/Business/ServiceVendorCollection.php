<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
class ServiceVendorCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($vendor) {
            return [
                'id' => $vendor->getKey() ?? null,
                'service_vendor_name' => $vendor->service_vendor_name ?? null,
                'service_vendor_slug' => $vendor->service_vendor_slug ?? null,
                'service_vendor_data' => $vendor->service_vendor_data ?? null,
                'service_vendor_logo_svg' => $vendor->getFirstMediaUrl('logo_svg') ?? null,
                'service_vendor_logo_png' => $vendor->getFirstMediaUrl('logo_png') ?? null,
                'enabled' => $vendor->enabled ?? null,
                'links' => $vendor->links,
                'created_at' => $vendor->created_at,
                'updated_at' => $vendor->updated_at,
            ];
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
