<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'code' => $this->code,
            'name' => $this->name,
            'chart_class_id' => $this->chart_class_id,
            'chart_class_name' => $this->chartClass?->name ?? null,
            'chart_type_data' => $this->chart_type_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
