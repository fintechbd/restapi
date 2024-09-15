<?php

namespace Fintech\RestApi\Http\Resources\Transaction\Charts;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAccountSummaryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'details' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'mobile' => $item->mobile,
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
                    'name',
                    'mobile',
                    'email',
                    'details',
                ],
                'labels' => [
                    'name' => 'Name',
                    'email' => 'Email',
                    'mobile' => 'Mobile',
                    'details' => 'Profile',
                ],
            ],
            'query' => $request->all(),
        ];
    }
}
