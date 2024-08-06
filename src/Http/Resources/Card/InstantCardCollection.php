<?php

namespace Fintech\RestApi\Http\Resources\Card;

use Carbon\Carbon;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class InstantCardCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($instantCard) {

            return [
                'id' => $instantCard->getKey(),
                'user_id' => $instantCard->user_id,
                'user_name' => $instantCard->user->name ?? null,
                'user_account_id' => $instantCard->user_account_id ?? null,
                'user_account_currency' => $instantCard->userAccount->user_account_data ?? (object) [],
                'type' => $instantCard->type ?? null,
                'scheme' => $instantCard->scheme ?? null,
                'name' => $instantCard->name ?? null,
                'number' => Str::mask(($instantCard->number ?? '1234-5678-9123-4567'), '*', 0, -4),
                'pin' => Str::mask(($instantCard->pin ?? '1234'), '*', 0),
                'cvc' => Str::mask(($instantCard->cvc ?? '123'), '*', 0),
                'provider' => $instantCard->provider ?? null,
                'status' => $instantCard->status ?? null,
                'note' => $instantCard->note ?? null,
                'balance' => $instantCard->balance ?? null,
                'instant_card_data' => $instantCard->instant_card_data ?? (object) [],
                'status' => $instantCard->status ?? null,
                'approver_id' => $instantCard->approver_id ?? null,
                'approver_name' => $instantCard->approver?->name ?? null,
                'issued_date_label' => Carbon::parse($instantCard->issued_at)->format('m/y'),
                'expired_date_label' => Carbon::parse($instantCard->expired_at)->format('m/y'),
                'status' => $instantCard->status,
                'links' => $instantCard->links,
                'issued_at' => $instantCard->issued_at,
                'expired_at' => $instantCard->expired_at,
                'updated_at' => $instantCard->updated_at,
                'updated_at' => $instantCard->updated_at,
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
