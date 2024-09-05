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
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
//        return $this->collection->map(function ($servicePackage) {
////            return [
////                'id' => $servicePackage->getKey() ?? null,
////                'service_id' => $servicePackage->service_id ?? null,
////                'service_name' => $servicePackage->service?->service_name ?? null,
////                'name' => $servicePackage->name ?? null,
////                'code' => $servicePackage->code ?? null,
////                'rate' => $servicePackage->rate ?? null,
////                'service_package_data' => $servicePackage->service_package_data ?? null,
////                'enabled' => $servicePackage->enabled ?? null,
////                'created_at' => $servicePackage->created_at,
////                'updated_at' => $servicePackage->updated_at,
////            ];
//        })->toArray();
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
