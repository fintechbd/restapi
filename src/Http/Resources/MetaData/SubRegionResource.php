<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubRegionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'region_id' => $this->region_id ?? null,
            'region_name' => ($this->region != null) ? $this->region->name : null,
            'name' => $this->name ?? null,
            'vendor_code' => $this->vendor_code ?? (object)[],
            'subregion_data' => $this->subregion_data ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'restored_at' => $this->restored_at,
        ];
    }
}
