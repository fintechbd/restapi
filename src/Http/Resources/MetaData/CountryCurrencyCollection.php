<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/**
 * @property-read Collection $currencies
 */
class CountryCurrencyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        $availableCurrencies = $this->collection['availableCurrencies'];
        $enabledCurrencies = $this->collection['enabledCurrencies'];

        return $availableCurrencies->map(function ($currency) use ($enabledCurrencies) {
            return [
                'id' => $currency->getKey(),
                'name' => $currency->currency_name,
                'code' => $currency->currency,
                'enabled' => in_array($currency->getKey(), $enabledCurrencies),
            ];
        })->toArray();
    }
}
