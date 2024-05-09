<?php

namespace Fintech\RestApi\Http\Resources\Banco;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @method getKey()
 *
 * @property int $bank_id
 * @property mixed $bank
 * @property string $name
 * @property array $bank_branch_data
 * @property mixed $links
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class BankBranchCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($bankBranch) {
            $data = [
                'id' => $bankBranch->getKey() ?? null,
                'country_id' => $this->bank->country_id ?? null,
                'country' => $this->bank->country->name ?? null,
                'bank_id' => $bankBranch->bank_id ?? null,
                'bank' => $bankBranch->bank->name ?? null,
                'name' => $bankBranch->name ?? null,
                'vendor_code' => $bankBranch->vendor_code ?? (object)[],
                'bank_branch_data' => $bankBranch->bank_branch_data ?? null,
                'enabled' => $bankBranch->enabled ?? null,
                'links' => $bankBranch->links,
                'created_at' => $bankBranch->created_at,
                'updated_at' => $bankBranch->updated_at,
            ];

            return $data;
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
