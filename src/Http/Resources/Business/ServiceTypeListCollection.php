<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceTypeListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        $services = $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'logo_svg' => $item->logo_svg,
                'logo_png' => $item->logo_png,
                'service_type_parent_id' => $item->service_type_parent_id ?? null,
                'service_type_name' => $item->service_type_name ?? null,
                'service_type_is_parent' => $item->service_type_is_parent ?? 'no',
                'service_type_slug' => $item->service_type_slug ?? null,
                'service_type_step' => $item->service_type_step ?? 1,
                'service_id' => $item->service_id ?? null,
                'service_data' => $item->service_data ?? [],
                'menu_position' => null,
            ];
        });

        return $services->toArray();
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'options' => [],
            'query' => $request->all(),
        ];
    }
}
