<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChartTypeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($chart_type) {
            return [
                'id' => $chart_type->getKey(),
                'code' => $chart_type->code,
                'name' => $chart_type->name,
                'chart_class_id' => $chart_type->chart_class_id,
                'chart_class_name' => $chart_type->chartClass?->name ?? null,
                'chart_type_data' => $chart_type->chart_type_data,
                'created_at' => $chart_type->created_at,
                'updated_at' => $chart_type->updated_at,
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
