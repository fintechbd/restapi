<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PolicyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($policy) {
            return [
                'id' => $policy->getKey(),
                'name' => $policy->name ?? null,
                'code' => $policy->code ?? null,
                'enabled' => $policy->enabled ?? false,
                'risk' => $policy->risk ?? RiskProfile::tryFrom('green'),
                'priority' => $policy->priority ?? RiskProfile::tryFrom('green'),
                'policy_data' => $policy->policy_data ?? [],
                'created_at' => $policy->created_at ?? null,
                'updated_at' => $policy->updated_at ?? null
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
