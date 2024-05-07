<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VerifyIdDocTypeResource extends JsonResource
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
            'valid' => $this->resource == null
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'message' => ($this->resource == null)
                ? 'This ID Document is valid.'
                : 'This ID Document is already exists with other account.',
            'query' => $request->all(),
        ];
    }
}
