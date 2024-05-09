<?php

namespace Fintech\RestApi\Http\Resources\Core;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiLogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($apiLog) {
            return [
                'id' => $apiLog->getKey(),
                'direction' => $apiLog->direction,
                'user_id' => $apiLog->user_id,
                'user_name' => ($apiLog->user) ? $apiLog->user->name : null,
                'method' => $apiLog->method,
                'host' => $apiLog->host,
                'url' => $apiLog->url,
                'type' => $apiLog->type,
                'status_code' => $apiLog->status_code,
                'status_text' => $apiLog->status_text,
                'user_agent' => $apiLog->user_agent,
                'created_at' => $apiLog->created_at,
                'links' => $apiLog->links,
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
