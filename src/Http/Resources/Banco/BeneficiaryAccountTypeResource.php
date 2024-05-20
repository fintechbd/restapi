<?php

namespace Fintech\RestApi\Http\Resources\Banco;

use Illuminate\Http\Resources\Json\JsonResource;

class BeneficiaryAccountTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey() ?? null,
            'bank_id' => $this->bank_id ?? null,
            'bank_name' => ($this->bank) ? $this->bank->name : null,
            'country_id' => $this->bank->country_id ?? null,
            'country_name' => $this->bank->country->name ?? null,
            'enabled' => $this->enabled ?? null,
            'beneficiary_account_types_data' => $this->beneficiary_account_types_data ?? null,
            'links' => $this->links,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
