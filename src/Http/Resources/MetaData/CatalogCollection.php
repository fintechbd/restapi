<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Fintech\Core\Enums\MetaData\CatalogType;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CatalogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {

            return [
                'id' => $item->getKey(),
                'name' => $item->name,
                'code' => $item->code,
                'type' => $item->type,
                'type_name' => CatalogType::value($item->type)->label(),
                'enabled' => $item->enabled,
                'countries' => $item->countries ? $item->countries->pluck('id')->toArray() : [],
                'vendor_code' => $item->vendor_code ?: [],
                'catalog_data' => $item->catalog_data ?: [],
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'deleted_at' => $item->deleted_at,
                'restored_at' => $item->restored_at,
                'links' => $item->links,
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
