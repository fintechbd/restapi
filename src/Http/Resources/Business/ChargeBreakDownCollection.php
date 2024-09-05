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
                'service_stat_id' => $chargeBreakDown->service_stat_id,
                'service_stat_name' => $chargeBreakDown->serviceStat?->service?->service_name ?? null,
                'service_id' => $chargeBreakDown->service_id,
                'service_name' => $chargeBreakDown->service?->service_name ?? null,
                'service_stat_role_id' => $chargeBreakDown->serviceStat->role_id ?? null,
                'service_stat_role_name' => $chargeBreakDown->serviceStat->role?->name ?? null,
                'service_stat_source_country_id' => $chargeBreakDown->serviceStat->source_country_id ?? null,
                'service_stat_source_country_name' => $chargeBreakDown->serviceStat->sourceCountry?->name ?? null,
                'service_stat_destination_country_id' => $chargeBreakDown->serviceStat->destination_country_id ?? null,
                'service_stat_destination_country_name' => $chargeBreakDown->serviceStat->destinationCountry?->name ?? null,
                'lower_limit' => $chargeBreakDown->lower_limit,
                'higher_limit' => $chargeBreakDown->higher_limit,
                'charge' => $chargeBreakDown->charge,
                'discount' => $chargeBreakDown->discount,
                'commission' => $chargeBreakDown->commission,
                'enabled' => $chargeBreakDown->enabled,
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
