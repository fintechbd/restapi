<?php

namespace Fintech\RestApi\Http\Resources\Banco;

use Fintech\Core\Facades\Core;
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

                'city_id' => $bankBranch->city_id ?? null,
                'city_name' => null,

                'state_id' => $bankBranch->state_id ?? null,
                'state_name' => null,

                'country_id' => null,
                'country_name' => null,

                'bank_id' => $bankBranch->bank_id ?? null,
                'bank_name' => $bankBranch->bank->name ?? null,

                'name' => $bankBranch->name ?? null,
                'location_no' => $bankBranch->location_no ?? null,
                'vendor_code' => $bankBranch->vendor_code ?? (object)[],
                'bank_branch_data' => $bankBranch->bank_branch_data ?? null,
                'enabled' => $bankBranch->enabled ?? null,
                'created_at' => $bankBranch->created_at,
                'updated_at' => $bankBranch->updated_at,
            ];

            if (Core::packageExists('MetaData')) {
                $data['country_id'] = ($bankBranch->bank) ? $bankBranch->bank->country_id : null;
                $data['country_name'] = ($bankBranch->bank->country) ? $bankBranch->bank->country->name : null;
                $data['city_name'] = ($bankBranch->city) ? $bankBranch->city->name : null;
                $data['state_name'] = ($bankBranch->state) ? $bankBranch->state->name : null;
            }

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
