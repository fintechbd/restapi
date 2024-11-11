<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Enums\Auth\RiskProfile;
use Illuminate\Http\Resources\Json\JsonResource;

class PolicyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name ?? null,
            'code' => $this->code ?? null,
            'enabled' => $this->enabled ?? false,
            'risk' => $this->risk ?? RiskProfile::tryFrom('green'),
            'priority' => $this->priority ?? RiskProfile::tryFrom('green'),
            'policy_data' => $this->policy_data ?? [],
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null
        ];
    }
}
