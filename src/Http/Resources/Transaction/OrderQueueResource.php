<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Facades\Core;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderQueueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->getKey(),
            'user_id' => $this->user_id,
            'user_name' => null,
            'order_id' => $this->order_id,
            'order_name' => $this->order->name,
            'created_at' => $this->created_at,
        ];

        if (Core::packageExists('Auth')) {
            $data['user_name'] = $this->user?->name ?? null;
        }

        return $data;
    }
}
