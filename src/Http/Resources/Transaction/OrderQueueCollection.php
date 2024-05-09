<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderQueueCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($order_queue) {
            $data = [
                'id' => $order_queue->getKey(),
                'user_id' => $order_queue->user_id,
                'user_name' => null,
                'order_id' => $order_queue->order_id,
                'order_name' => $order_queue?->order?->name ?? null,
                'links' => $order_queue->links,
                'created_at' => $order_queue->created_at,
            ];

            if (Core::packageExists('Auth')) {
                $data['user_name'] = $order_queue->user?->name ?? null;
            }

            return $data;

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
