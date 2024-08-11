<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($role) {
            $return = [
                'id' => $role->getKey(),
                //                'team_id' => $role->team_id ?? null,
                //                'team_name' => ($role->team != null) ? $role->team->name : null,
                'name' => $role->name ?? null,
                'guard_name' => $role->guard_name ?? null,
                'permissions' => [],
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
                'deleted_at' => $role->deleted_at,
                'restored_at' => $role->restored_at,
                ];

            if (! $role->permissions->isEmpty()) {
                foreach ($role->permissions as $permission) {
                    $return['permissions'][] = [
                        'id' => $permission->getKey(),
                        'name' => $permission->name ?? null,
                        'guard_name' => $permission->guard_name ?? null,
                        'created_at' => $permission->pivot->created_at,
                        'updated_at' => $permission->pivot->updated_at,
                    ];
                }
            }

            return $return;

        })->toArray();
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        $teams = [];

        //        Auth::team()->list(['paginate' => false])
        //            ->each(function ($team) use (&$teams) {
        //                $teams[$team->getKey()] = $team->name;
        //            });

        return [
            'options' => [
                'dir' => Constant::SORT_DIRECTIONS,
                'per_page' => Constant::PAGINATE_LENGTHS,
                //                'team_id' => $teams,
                'sort' => ['id', /*'team_id',*/ 'name', 'guard_name', 'created_at', 'updated_at'],
            ],
            'query' => $request->all(),
        ];
    }
}
