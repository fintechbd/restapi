<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name ?? null,
            'guard_name' => $this->guard_name ?? null,
            'permissions' => $this->permissions->isEmpty() ? [] : $this->permissions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'restored_at' => $this->restored_at,
        ];
    }
}
