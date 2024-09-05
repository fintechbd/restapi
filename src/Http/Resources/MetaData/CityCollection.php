<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CityCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($city) {
            return [
                'id' => $city->getKey(),
                'name' => $city->name,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
                'enabled' => $city->enabled,
                'vendor_code' => $city->vendor_code ?? (object)[],
                'city_data' => $city->city_data,
                'country_id' => $city->country_id ?? null,
                'country_name' => ($city->country != null) ? $city->country->name : null,
                'state_id' => $city->state_id ?? null,
                'state_name' => ($city->state != null) ? $city->state->name : null,
                'created_at' => $city->created_at,
                'updated_at' => $city->updated_at,
                'deleted_at' => $city->deleted_at,
                'restored_at' => $city->restored_at,
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
