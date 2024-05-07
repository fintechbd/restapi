<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Facades\Core;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->getKey(),
            'source_country_id' => $this->source_country_id,
            'source_country_name' => null,
            'destination_country_id' => $this->destination_country_id,
            'destination_country_name' => null,
            'service_id' => $this->service_id,
            'service_name' => $this->service?->service_name ?? null,
            'rate' => $this->rate,
            'is_default' => $this->is_default,
            'currency_rate_data' => $this->currency_rate_data,
            'links' => $this->links,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (Core::packageExists('MetaData')) {
            $data['source_country_name'] = $this->sourceCountry?->name ?? null;
            $data['destination_country_name'] = $this->destinationCountry?->name ?? null;
        }

        return $data;
    }
}
