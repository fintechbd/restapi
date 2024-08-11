<?php

namespace Fintech\RestApi\Http\Resources\MetaData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $links = [
            'show' => action_link(route('metadata.fund-sources.show', $this->getKey()), __('restapi::messages.action.show'), 'get'),
            'update' => action_link(route('metadata.fund-sources.update', $this->getKey()), __('restapi::messages.action.update'), 'put'),
            'destroy' => action_link(route('metadata.fund-sources.destroy', $this->getKey()), __('restapi::messages.action.destroy'), 'delete'),
            'restore' => action_link(route('metadata.fund-sources.restore', $this->getKey()), __('restapi::messages.action.restore'), 'post'),
        ];

        if ($this->getAttribute('deleted_at') == null) {
            unset($links['restore']);
        } else {
            unset($links['destroy']);
        }

        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'code' => $this->code,
            'enabled' => $this->enabled,
            'vendor_code' => $this->vendor_code,
            'catalog_data' => $this->catalog_data,
            'countries' => $this->countries != null ? $this->countries->pluck('name', 'id')->toArray() : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'restored_at' => $this->restored_at,
        ];
    }
}
