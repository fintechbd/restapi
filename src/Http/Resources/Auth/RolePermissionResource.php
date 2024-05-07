<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Fintech\Auth\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property-read Collection $permissions
 */
class RolePermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $rolePermissions = $this->permissions->pluck('id')->toArray();

        $data = [];

        Auth::permission()
            ->list(['paginate' => false])
            ->each(function ($permission) use (&$data, $rolePermissions) {
                $data[] = [
                    'id' => $permission->getKey(),
                    'name' => $permission->name,
                    'enabled' => in_array($permission->getKey(), $rolePermissions),
                ];
            });

        return $data;
    }
}
