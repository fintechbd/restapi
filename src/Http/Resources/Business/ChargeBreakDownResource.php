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
            'service_slug' => $this->service_slug,
            'charge_break_down_lower' => $this->charge_break_down_lower,
            'charge_break_down_higher' => $this->charge_break_down_higher,
            'charge_break_down_charge' => $this->charge_break_down_charge,
            'charge_break_down_discount' => $this->charge_break_down_discount,
            'charge_break_down_commission' => $this->charge_break_down_commission,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
