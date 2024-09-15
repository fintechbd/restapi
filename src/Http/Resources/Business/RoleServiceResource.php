<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Business\Facades\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property-read Collection $services
 */
class RoleServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $roleServices = $this->services->pluck('id')->toArray();

        $data = [];

        Business::service()
            ->list(['paginate' => false])
            ->each(function ($service) use (&$data, $roleServices) {
                $data[] = [
                    'id' => $service->getKey(),
                    'name' => $service->service_name,
                    'enabled' => in_array($service->getKey(), $roleServices),
                ];
            });

        return $data;
    }
}
