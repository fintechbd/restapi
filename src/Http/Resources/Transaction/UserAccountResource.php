<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Facades\Core;
use Fintech\Transaction\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function currency;

/**
 * @see UserAccount
 */
class UserAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->getKey(),
            'name' => $this->name,
            'account_no' => $this->account_no,
            'user_id' => $this->user_id,
            'user_name' => null,
            'country_id' => $this->country_id,
            'country_name' => null,
            'logo_svg' => null,
            'logo_png' => null,
            'user_account_data' => $this->user_account_data ?? (object)[],
            'currency' => $user_account->user_account_data['currency'] ?? null,
            'currency_name' => $user_account->user_account_data['currency_name'] ?? null,
            'currency_symbol' => $user_account->user_account_data['currency_symbol'] ?? null,
            'deposit_amount' => $user_account->user_account_data['deposit_amount'] ?? 0,
            'available_amount' => $user_account->user_account_data['available_amount'] ?? 0,
            'spent_amount' => $user_account->user_account_data['spent_amount'] ?? 0,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        $data['deposit_amount_formatted'] = currency($data['deposit_amount'], $data['currency'])->format();
        $data['available_amount_formatted'] = currency($data['available_amount'], $data['currency'])->format();
        $data['spent_amount_formatted'] = currency($data['spent_amount'], $data['currency'])->format();

        if (Core::packageExists('Auth')) {
            $data['user_name'] = $this->user?->name ?? null;
        }
        if (Core::packageExists('MetaData')) {
            $data['country_name'] = $this->country?->name ?? null;
            $data['logo_svg'] = $this->country?->getFirstMediaUrl('logo_svg') ?? null;
            $data['logo_png'] = $this->country?->getFirstMediaUrl('logo_png') ?? null;
        }

        return $data;
    }
}
