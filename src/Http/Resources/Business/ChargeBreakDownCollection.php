<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChargeBreakDownCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($chargeBreakDown) {

            return [
                'id' => $chargeBreakDown->getKey(),
                'service_stat_id' => $chargeBreakDown->serviceStat_id,
                'service_stat_name' => $chargeBreakDown->serviceStat?->service?->service_name ?? null,
                'service_slug' => $chargeBreakDown->service_slug,
                'charge_break_down_lower' => $chargeBreakDown->charge_break_down_lower,
                'charge_break_down_higher' => $chargeBreakDown->charge_break_down_higher,
                'charge_break_down_charge' => $chargeBreakDown->charge_break_down_charge,
                'charge_break_down_discount' => $chargeBreakDown->charge_break_down_discount,
                'charge_break_down_commission' => $chargeBreakDown->charge_break_down_commission,
                'enabled' => $chargeBreakDown->enabled,
                'links' => $chargeBreakDown->links,
                'created_at' => $chargeBreakDown->created_at,
                'updated_at' => $chargeBreakDown->updated_at,
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
