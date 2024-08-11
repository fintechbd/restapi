<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($state) {
            return [
                'id' => $state->getKey(),
                'name' => $state->name,
                'vendor_code' => $state->vendor_code ?? (object) [],
                'state_data' => $state->state_data,
                'latitude' => $state->latitude,
                'longitude' => $state->longitude,
                'enabled' => $state->enabled,
                'country_id' => $state->country_id ?? null,
                'country_name' => ($state->country != null) ? $state->country->name : null,
                'created_at' => $state->created_at,
                'updated_at' => $state->updated_at,
                'deleted_at' => $state->deleted_at,
                'restored_at' => $state->restored_at,
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
