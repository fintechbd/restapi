<?php

namespace Fintech\RestApi\Http\Resources\Banco;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BeneficiaryAccountTypeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($accountType) {
            return [
                'id' => $accountType->getKey() ?? null,
                'bank_id' => $accountType->bank_id ?? null,
                'bank_name' => ($accountType->bank) ? $accountType->bank->name : null,
                'country_id' => $accountType->bank->country_id ?? null,
                'country_name' => $accountType->bank->country->name ?? null,
                'enabled' => $accountType->enabled ?? null,
                'beneficiary_account_types_data' => $accountType->beneficiary_account_types_data ?? null,
                'links' => $accountType->links,
                'created_at' => $accountType->created_at,
                'updated_at' => $accountType->updated_at,
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
