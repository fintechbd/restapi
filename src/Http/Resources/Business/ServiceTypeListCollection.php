<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceTypeListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        /*        return $this->collection->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'parent_id' => $item->service_type_parent_id ?? null,
                        'type' => strtolower($item->service_type_is_parent) == 'yes' ? 'service-type' : 'service',
                        'logo_svg' => $item->logo_svg,
                        'logo_png' => $item->logo_png,
                        'name' => $item->service_type_name ?? null,
                        'service_type_name' => $item->service_type_name ?? null,
                    ];
                })->toArray();*/

        return parent::toArray($request);
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
