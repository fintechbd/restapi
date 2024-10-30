<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class ComplianceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
