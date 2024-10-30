<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ComplianceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($compliance) {
            return [
                'id' => $compliance->getKey(),
                'code' => $compliance->code ?? null,
                'name' => $compliance->name ?? null,
                'score' => $compliance->score ?? null,
                'risk' => $compliance->risk ?? null,
                'priority' => $compliance->priority ?? null,
                'description' => $compliance->description ?? null,
                'compliance_data' => $compliance->compliance_data ?? (object)[],
                'user_name' => $compliance->user_name ?? null,
                'user_mobile' => $compliance->user_mobile ?? null,
                'order_source_country_name' => $compliance->source_country_name ?? null,
                'order_destination_country_name' => $compliance->destination_country_name ?? null,
                'order_number' => $compliance->order_number ?? null,
                'order_status' => $compliance->order_status ?? null,
                'created_at' => $compliance->created_at ?? null,
                'updated_at' => $compliance->updated_at ?? null
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
