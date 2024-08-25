<?php

namespace Fintech\RestApi\Http\Resources\Reload\Charts;

use Fintech\Core\Enums\Auth\UserStatus;
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
        return parent::toArray($request);
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
            ],
            'query' => $request->all(),
        ];
    }
}
