<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceFieldCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($serviceField) {
            return [
                'id' => $serviceField->getKey(),
                'service_id' => $serviceField->service_id ?? null,
                'service_name' => $serviceField->service?->service_name ?? null,
                'name' => $serviceField->name ?? null,
                'label' => $serviceField->label ?? null,
                'type' => $serviceField->type ?? null,
                'options' => $serviceField->options ?? [],
                'value' => $serviceField->value ?? null,
                'hint' => $serviceField->hint ?? null,
                'required' => $serviceField->required ?? false,
                'reserved' => $serviceField->reserved ?? false,
                'enabled' => $serviceField->enabled ?? false,
                'validation' => $serviceField->validation ?? null,
                'service_field_data' => $serviceField->service_field_data ?? [],
                'created_at' => $serviceField->created_at ?? null,
                'updated_at' => $serviceField->updated_at ?? null,
            ];
        });
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
