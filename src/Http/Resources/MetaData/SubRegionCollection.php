<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Fintech\Core\Supports\Constant;
use Fintech\MetaData\Facades\MetaData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubRegionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($subregion) {
            return [
                'id' => $subregion->getKey(),
                'region_id' => $subregion->region_id ?? null,
                'region_name' => ($subregion->region != null) ? $subregion->region->name : null,
                'name' => $subregion->name ?? null,
                'vendor_code' => $subregion->vendor_code ?? (object) [],
                'subregion_data' => $subregion->subregion_data ?? null,
                'created_at' => $subregion->created_at,
                'updated_at' => $subregion->updated_at,
                'deleted_at' => $subregion->deleted_at,
                'restored_at' => $subregion->restored_at,
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
        $regions = [];

        MetaData::region()->list(['paginate' => false])
            ->each(function ($region) use (&$regions) {
                $regions[$region->getKey()] = $region->name;
            });

        return [
            'options' => [
                'dir' => Constant::SORT_DIRECTIONS,
                'per_page' => Constant::PAGINATE_LENGTHS,
                'sort' => ['id', 'name', 'created_at', 'updated_at'],
                'region_id' => $regions,
            ],
            'query' => $request->all(),
        ];
    }
}
