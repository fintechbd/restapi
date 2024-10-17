<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Fintech\Auth\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => ($this->user_id != null) ? Auth::user()->find($this->user_id)?->name ?? null : null,
            'event' => $this->event,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'url' => $this->url,
            'auditable_id' => $this->auditable_id,
            'auditable_type' => $this->auditable_type,
            'tags' => $this->tags,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
