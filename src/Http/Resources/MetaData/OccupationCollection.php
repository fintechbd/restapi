<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Fintech\Core\Supports\Constant;
use Fintech\MetaData\Facades\MetaData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OccupationCollection extends ResourceCollection
{
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
                'countries' => MetaData::country()->list(['paginate' => false])->pluck('name', 'id')->toArray(),
                'per_page' => Constant::PAGINATE_LENGTHS,
                'sort' => ['id', 'name', 'code', 'enabled', 'created_at', 'updated_at'],
            ],
            'query' => $request->all(),
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {

            $links = [
                'show' => action_link(route('metadata.occupations.show', $item->getKey()), __('restapi::messages.action.show'), 'get'),
                'update' => action_link(route('metadata.occupations.update', $item->getKey()), __('restapi::messages.action.update'), 'put'),
                'destroy' => action_link(route('metadata.occupations.destroy', $item->getKey()), __('restapi::messages.action.destroy'), 'delete'),
                'restore' => action_link(route('metadata.occupations.restore', $item->getKey()), __('restapi::messages.action.restore'), 'post'),
            ];

            if ($item->getAttribute('deleted_at') == null) {
                unset($links['restore']);
            } else {
                unset($links['destroy']);
            }

            return [
                'id' => $item->getKey(),
                'name' => $item->name,
                'code' => $item->code,
                'enabled' => $item->enabled,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'deleted_at' => $item->deleted_at,
                'restored_at' => $item->restored_at,
                'links' => $links,
            ];
        })->toArray();
    }
}
