<?php

namespace Fintech\RestApi\Http\Resources\Auth\Charts;

use Fintech\Core\Enums\Auth\UserStatus;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserStatusSummaryCollection extends ResourceCollection
{
    private int $total = 0;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->total = 0;

        return $this->collection->transform(function ($item) {
            $this->total += $item->count;

            return [
                'total' => number_format($item->count),
                'label' => UserStatus::name($item->status)->label(),
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
                'sort' => ['count', 'status'],
                'filter' => [],
                'columns' => [
                    'label',
                    'total',
                ],
                'labels' => [
                    'total' => 'Total',
                    'label' => 'Stage',
                ],
            ],
            'meta' => [
                'total' => number_format($this->total),
                'label' => 'Total',
            ],
            'query' => $request->all(),
        ];
    }
}
