<?php

namespace Fintech\RestApi\Http\Resources\Core;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiLogResource extends JsonResource
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
            'direction' => $this->direction,
            'user_id' => $this->user_id,
            'user_name' => ($this->user_id != null) ? \Fintech\Auth\Facades\Auth::user()->find($this->user_id)?->name ?? null : null,
            'method' => $this->method,
            'host' => $this->host,
            'url' => $this->url,
            'type' => $this->type,
            'status_code' => $this->status_code,
            'status_text' => $this->status_text,
            'request' => $this->request,
            'response' => $this->response,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at,
        ];
    }
}
