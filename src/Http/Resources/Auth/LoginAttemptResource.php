<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginAttemptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
            return [
                'id' => $this->getKey() ?? null,
                'user_id' => $this->user_id ?? null,
                'user_name' => ($this->user) ? $this->user->name : null,
                'user_photo' => ($this->user) ? $this->user?->getFirstMediaUrl('photo') : null,
                'ip' => $this->ip ?? null,
                'mac' => $this->mac ?? null,
                'agent' => $this->agent ?? null,
                'platform' => $this->platform ?? null,
                'address' => $this->address ?? null,
                'city' => $this->city ?? null,
                'city_id' => $this->city_id ?? null,
                'state' => $this->state ?? null,
                'state_id' => $this->state_id ?? null,
                'country' => $this->country ?? null,
                'country_id' => $this->country_id ?? null,
                'latitude' => $this->latitude ?? null,
                'longitude' => $this->longitude ?? null,
                'status' => $this->status ?? null,
                'login_attempt_data' => $this->login_attempt_data ?? null,
                'links' => $this->links,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
    }
}
