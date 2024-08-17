<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceFieldResource extends JsonResource
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
            'service_id' => $this->service_id ?? null,
            'service_name' => $this->service?->service_name ?? null,
            'name' => $this->name ?? null,
            'label' => $this->label ?? null,
            'type' => $this->type ?? null,
            'options' => $this->options ?? [],
            'value' => $this->value ?? null,
            'hint' => $this->hint ?? null,
            'required' => $this->required ?? false,
            'reserved' => $this->reserved ?? false,
            'enabled' => $this->enabled ?? false,
            'validation' => $this->validation ?? null,
            'service_field_data' => $this->service_field_data ?? [],
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];
    }
}
