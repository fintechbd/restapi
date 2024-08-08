<?php

namespace Fintech\RestApi\Http\Resources\Card;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PrepaidCardResource extends JsonResource
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
            'id' => $this->getKey(),
            'user_id' => $this->user_id,
            'user_name' => $this->user->name ?? null,
            'user_account_id' => $this->user_account_id ?? null,
            'user_account_currency' => $this->userAccount->user_account_data ?? (object) [],
            'type' => $this->type ?? null,
            'scheme' => $this->scheme ?? null,
            'name' => $this->name ?? null,
            'number' => Str::mask(($this->number ?? '1234-5678-9123-4567'), '*', 0, -4),
            'pin' => Str::mask(($this->pin ?? '1234'), '*', 0),
            'cvc' => Str::mask(($this->cvc ?? '123'), '*', 0),
            'provider' => $this->provider ?? null,
            'status' => $this->status ?? null,
            'note' => $this->note ?? null,
            'balance' => $this->balance ?? null,
            'instant_card_data' => $this->instant_card_data ?? (object) [],
            'status' => $this->status ?? null,
            'approver_id' => $this->approver_id ?? null,
            'approver_name' => $this->approver?->name ?? null,
            'issued_date_label' => Carbon::parse($this->issued_at)->format('m/y'),
            'expired_date_label' => Carbon::parse($this->expired_at)->format('m/y'),
            'status' => $this->status,
            'links' => $this->links,
            'issued_at' => $this->issued_at,
            'expired_at' => $this->expired_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
