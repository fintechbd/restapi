<?php

namespace Fintech\RestApi\Http\Resources\Core;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PackageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->toArray();
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
                'enabled' => [true, false],
            ],
            'query' => $request->all(),
        ];
    }
}
