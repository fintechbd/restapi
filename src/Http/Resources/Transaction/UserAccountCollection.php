<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAccountCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($user_account) {
            $data = [
                'id' => $user_account->getKey(),
                'name' => $user_account->name,
                'account_no' => $user_account->account_no,
                'user_id' => $user_account->user_id,
                'user_name' => null,
                'country_id' => $user_account->country_id,
                'country_name' => null,
                'logo_svg' => null,
                'logo_png' => null,
                'currency' => $user_account->user_account_data['currency'] ?? null,
                'currency_name' => $user_account->user_account_data['currency_name'] ?? null,
                'currency_symbol' => $user_account->user_account_data['currency_symbol'] ?? null,
                'deposit_amount' => $user_account->user_account_data['deposit_amount'] ?? 0,
                'available_amount' => $user_account->user_account_data['available_amount'] ?? 0,
                'spent_amount' => $user_account->user_account_data['spent_amount'] ?? 0,
                'enabled' => $user_account->enabled,
                'created_at' => $user_account->created_at,
                'updated_at' => $user_account->updated_at,
            ];

            if (Core::packageExists('Auth')) {
                $data['user_name'] = $user_account->user?->name ?? null;
            }
            if (Core::packageExists('MetaData')) {
                $data['country_name'] = $user_account->country?->name ?? null;
                $data['logo_svg'] = $user_account->country?->getFirstMediaUrl('logo_svg') ?? null;
                $data['logo_png'] = $user_account->country?->getFirstMediaUrl('logo_png') ?? null;
            }

            return $data;

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
