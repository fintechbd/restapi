<?php

namespace Fintech\RestApi\Http\Resources\Reload\Charts;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DepositPartnerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
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
                    'service_name',
                    'account_number',
                    'limits',
                    'charge'
                ],
                'labels' => [
                    'service_type' => 'Mode',
                    'service_name' => 'Bank Name',
                    'account_number' => 'A/C Number',
                    'limits' => 'Limits(CAD)',
                    'charge' => 'Fee/Charge',
                ]
            ],
            'query' => $request->all(),
        ];
    }
}
