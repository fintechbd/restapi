<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChartEntryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($chart_entry) {
            return [
                'id' => $chart_entry->getKey(),
                'code' => $chart_entry->code,
                'name' => $chart_entry->name,
                'chart_type_id' => $chart_entry->chart_type_id,
                'chart_type_name' => $chart_entry->chartType?->name ?? null,
                'chart_type_data' => $chart_entry->chart_entry_data,
                'links' => $chart_entry->links,
                'created_at' => $chart_entry->created_at,
                'updated_at' => $chart_entry->updated_at,
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
