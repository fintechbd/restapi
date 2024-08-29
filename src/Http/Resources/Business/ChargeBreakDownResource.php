<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargeBreakDownResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'service_stat_id' => $this->serviceStat_id,
            'service_stat_name' => $this->serviceStat?->service?->service_name ?? null,
            'service_id' => $this->service_id,
            'service_name' => $this->service?->service_name ?? null,
            'service_stat_role_id' => $this->serviceStat->role_id ?? null,
            'service_stat_role_name' => $this->serviceStat->role?->name ?? null,
            'service_stat_source_country_id' => $this->serviceStat->source_country_id ?? null,
            'service_stat_source_country_name' => $this->serviceStat->sourceCountry?->name ?? null,
            'service_stat_destination_country_id' => $this->serviceStat->destination_country_id ?? null,
            'service_stat_destination_country_name' => $this->serviceStat->destinationCountry?->name ?? null,
            'lower_limit' => $this->lower_limit,
            'higher_limit' => $this->higher_limit,
            'charge' => $this->charge,
            'discount' => $this->discount,
            'commission' => $this->commission,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
