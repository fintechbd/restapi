<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Fintech\MetaData\Facades\MetaData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CurrencyRateCollection extends ResourceCollection
{
    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        //        $sourceCountries = [];
        //        $destinationCountries = [];
        //
        //        if (Core::packageExists('MetaData')) {
        //            $sourceCountries = MetaData::country()->list(['is_serving' => true, 'paginate' => false])->toArray();
        //        }

        return [
            'options' => [
                'dir' => Constant::SORT_DIRECTIONS,
                'per_page' => Constant::PAGINATE_LENGTHS,
                //                'source_country_id' => $sourceCountries,
                //                'destination_country_id' => $destinationCountries,
                'sort' => ['id', 'source_country_id', 'destination_country_id', 'service_id', 'rate', 'created_at', 'updated_at'],
            ],
            'query' => $request->all(),
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($currencyRate) {

            $data = [
                'id' => $currencyRate->getKey(),
                'source_country_id' => $currencyRate->source_country_id,
                'source_country_name' => null,
                'destination_country_id' => $currencyRate->destination_country_id,
                'destination_country_name' => null,
                'service_id' => $currencyRate->service_id,
                'service_name' => $currencyRate->service?->service_name ?? null,
                'rate' => $currencyRate->rate,
                'is_default' => $currencyRate->is_default,
                'currency_rate_data' => $currencyRate->currency_rate_data,
                'created_at' => $currencyRate->created_at,
                'updated_at' => $currencyRate->updated_at,
            ];

            if (Core::packageExists('MetaData')) {
                $data['source_country_name'] = $currencyRate->sourceCountry?->name ?? null;
                $data['destination_country_name'] = $currencyRate->destinationCountry?->name ?? null;
            }

            return $data;
        })->toArray();
    }
}
