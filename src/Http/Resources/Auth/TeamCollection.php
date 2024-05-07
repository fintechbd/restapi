<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TeamCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($team) {
            $return = [
                'id' => $team->getKey(),
                'name' => $team->name ?? null,
                'roles' => [],
                'created_at' => $team->created_at,
                'updated_at' => $team->updated_at,
                'links' => $team->links,
            ];

            if (! $team->roles->isEmpty()) {
                foreach ($team->roles as $role) {
                    $return['roles'][] = [
                        'id' => $role->getKey(),
                        'name' => $role->name ?? null,
                        'guard_name' => $role->guard_name ?? null,
                        'created_at' => $role->created_at,
                        'updated_at' => $role->updated_at,
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
