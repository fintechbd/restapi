<?php

namespace Fintech\RestApi\Http\Resources\Auth\Charts;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserRoleSummaryCollection extends ResourceCollection
{
    public int $total = 0;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return $this->collection->map(function ($item) {
            $this->total += $item->count;
            return [
                'total' => number_format($item->count),
                'name' => $item->name,
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
                'sort' => ['count', 'name'],
                'filter' => [],
                'columns' => [
                    'name',
                    'total',
                ],
                'labels' => [
                    'total' => 'Total',
                    'name' => 'Role',
                ],
            ],
            'meta' => [
                'total' => number_format($this->total),
                'label' => 'Total'
            ],
            'query' => $request->all(),
        ];
    }
}
