<?php

namespace Fintech\RestApi\Http\Resources\Core;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigurationResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(Request $request)
    {
        return $this->resource;
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
                'package' => config('fintech.core.packages'),
            ],
            'query' => $request->all(),
        ];
    }
}
