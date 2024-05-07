<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartEntryResource extends JsonResource
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
            'chart_type_id' => $this->chart_type_id,
            'chart_type_name' => $this->chartType?->name ?? null,
            'chart_entry_data' => $this->chart_entry_data,
            'links' => $this->links,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
