<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LoginAttemptCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($attempt) {
            return [
                'id' => $attempt->getKey() ?? null,
                'user_id' => $attempt->user_id ?? null,
                'user_name' => ($attempt->user) ? $attempt->user->name : null,
                'user_photo' => ($attempt->user) ? $attempt->user?->getFirstMediaUrl('photo') : null,
                'ip' => $attempt->ip ?? null,
                'mac' => $attempt->mac ?? null,
                'agent' => $attempt->agent ?? null,
                'platform' => $attempt->platform ?? null,
                'address' => $attempt->address ?? null,
                'city' => $attempt->city ?? null,
                'city_id' => $attempt->city_id ?? null,
                'state' => $attempt->state ?? null,
                'state_id' => $attempt->state_id ?? null,
                'country' => $attempt->country ?? null,
                'country_id' => $attempt->country_id ?? null,
                'latitude' => $attempt->latitude ?? null,
                'longitude' => $attempt->longitude ?? null,
                'status' => $attempt->status ?? null,
                'note' => $attempt->note ?? null,
                'login_attempt_data' => $attempt->login_attempt_data ?? null,
                'created_at' => $attempt->created_at,
                'updated_at' => $attempt->updated_at,
            ];
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
