<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class ComplianceResource extends JsonResource
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
            'code' => $this->code ?? null,
            'name' => $this->name ?? null,
            'score' => $this->score ?? null,
            'risk' => $this->risk ?? null,
            'priority' => $this->priority ?? null,
            'description' => $this->order?->description ?? null,
            'compliance_data' => $this->compliance_data ?? (object) [],
            'user_name' => $this->order?->user?->name ?? null,
            'user_mobile' => $this->order?->user?->mobile ?? null,
            'order_source_country_name' => $this->order?->sourceCountry?->name ?? null,
            'order_destination_country_name' => $this->order?->destinationCountry?->name ?? null,
            'order_number' => $this->order?->order_number ?? null,
            'order_status' => $this->order?->status ?? null,
            'order_data' => $this->order?->order_data ?? null,
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
            //                'timestamp' => $this->timestamp ?? null,
        ];
    }
}
