<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property mixed $service_vendor_id
 * @property mixed $serviceVendor
 * @property mixed $service_id
 * @property mixed $service
 * @property mixed $service_slug
 * @property mixed $source_country_id
 * @property mixed $sourceCountry
 * @property mixed $destination_country_id
 * @property mixed $destinationCountry
 * @property mixed $service_stat_data
 * @property mixed $enabled
 * @property mixed $links
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @method getKey()
 */
class ServiceStatCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($serviceStat) {
            $data = [
                'id' => $serviceStat->getKey() ?? null,
                'service_vendor_id' => $serviceStat->service_vendor_id ?? null,
                'service_vendor_name' => $serviceStat->serviceVendor?->service_vendor_name ?? null,
                'role_id' => $serviceStat->role_id ?? null,
                'role_name' => null,
                'service_id' => $serviceStat->service_id ?? null,
                'service_name' => $serviceStat->service->service_name ?? null,
                'service_slug' => $serviceStat->service_slug ?? null,
                'source_country_id' => $serviceStat->source_country_id ?? null,
                'source_country' => $serviceStat->sourceCountry->name ?? null,
                'destination_country_id' => $serviceStat->destination_country_id ?? null,
                'destination_country' => $serviceStat->destinationCountry->name ?? null,
                'service_stat_data' => $serviceStat->service_stat_data ?? null,
                'enabled' => $serviceStat->enabled ?? null,
                'links' => $serviceStat->links,
                'created_at' => $serviceStat->created_at,
                'updated_at' => $serviceStat->updated_at,
            ];

            if (Core::packageExists('Auth')) {
                $data['role_name'] = $serviceStat->role->name ?? null;
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
