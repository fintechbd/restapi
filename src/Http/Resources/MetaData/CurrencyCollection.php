<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CurrencyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($country) {
            return [
                'id' => $country->getKey(),
                'country_id' => $country->getKey(),
                'country_name' => $country->name ?? null,
                'name' => $country->currency_name ?? null,
                'code' => $country->currency ?? null,
                'symbol' => $country->currency_symbol ?? null,
                'logo_svg' => $country->getFirstMediaUrl('logo_svg'),
                'logo_png' => $country->getFirstMediaUrl('logo_png'),
                'enabled' => $country->country_data['multi_currency_enabled'] ?? false,
                'created_at' => $country->created_at,
                'updated_at' => $country->updated_at,
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
