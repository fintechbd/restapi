<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Fintech\Auth\Facades\Auth;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AuditCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'user_name' => ($item->user_id != null) ? Auth::user()->find($item->user_id)?->name ?? null : null,
                'event' => $item->event,
                'ip_address' => $item->ip_address,
                'user_agent' => $item->user_agent,
                'url' => $item->url,
                'auditable_id' => $item->auditable_id,
                'auditable_type' => class_basename($item->auditable_type),
                'tags' => $item->tags,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
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
