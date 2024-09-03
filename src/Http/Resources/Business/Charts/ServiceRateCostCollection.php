<?php

namespace Fintech\RestApi\Http\Resources\Business\Charts;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceRateCostCollection extends ResourceCollection
{
    private int $total;

    private int $sum;
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return $item;
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
                'sort' => ['count', 'status'],
                'filter' => [],
                'columns' => [
                    'service_type',
                    'currency',
                    'currency_rate',
                    'charge',
                ],
                'labels' => [
                    'currency' => 'Country/Currency',
                    'currency_rate' => 'Ex Rate',
                    'charge' => 'Fee/Charge',
                    'service_type' => 'Transaction Type',
                ],
            ],
            'meta' => [
                'total' => number_format($this->total),
                'name' => 'Total'
            ],
            'query' => $request->all(),
        ];
    }
}
