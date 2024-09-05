<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicePackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
//        return [
//            'id' => $this->getKey() ?? null,
//            'service_id' => $this->service_id ?? null,
//            'service_name' => $this->service?->service_name ?? null,
//            'name' => $this->name ?? null,
//            'code' => $this->code ?? null,
//            'rate' => $this->rate ?? null,
//            'service_package_data' => $this->service_package_data ?? null,
//            'enabled' => $this->enabled ?? null,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//        ];
    }
}
