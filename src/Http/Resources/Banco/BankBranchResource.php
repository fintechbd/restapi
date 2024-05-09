<?php

namespace Fintech\RestApi\Http\Resources\Banco;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
class BankBranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->getKey() ?? null,
            'country_id' => $this->bank->country_id ?? null,
            'country' => $this->bank->country->name ?? null,
            'bank_id' => $this->bank_id ?? null,
            'bank' => $this->bank->name ?? null,
            'name' => $this->name ?? null,
            'bank_branch_data' => $this->bank_branch_data ?? null,
            'vendor_code' => $this->vendor_code ?? (object) [],
            'enabled' => $this->enabled ?? null,
            'links' => $this->links,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return $data;
    }
}
