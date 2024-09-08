<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServicePackageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($servicePackage) {
            return [
                'id' => $servicePackage->getKey(),
                'service_id' => $servicePackage->service_id,
                'service_name' => $servicePackage->service?->service_name ?? null,
                'service_logo_svg' => $servicePackage?->service->getFirstMediaUrl('logo_svg') ?? null,
                'service_logo_png' => $servicePackage?->service->getFirstMediaUrl('logo_png') ?? null,
                'country_id' => $servicePackage->country_id,
                'country_name' => $servicePackage->country?->name ?? null,
                'country_currency' => $servicePackage->country?->currency ?? null,
                'name' => $servicePackage->name,
                'slug' => $servicePackage->slug,
                'description' => $servicePackage->description,
                'amount' => $servicePackage->amount,
                'amount_formatted' => (string)\currency($servicePackage->amount, $servicePackage->country?->currency),
                'enabled' => $servicePackage->enabled,
                'type' => $servicePackage->type,
                'service_package_data' => $servicePackage->service_package_data,
                'created_at' => $servicePackage->created_at,
                'updated_at' => $servicePackage->updated_at,
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
                'type' => ['combo', 'internet', 'sms', 'voice'],
            ],
            'query' => $request->all(),
        ];
    }
}
