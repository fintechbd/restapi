<?php

namespace Fintech\RestApi\Http\Resources\Banco;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @method getKey()
 * @method getFirstMediaUrl(string $string)
 *
 * @property int $country_id
 * @property string $country
 * @property int $beneficiary_type_id
 * @property string $beneficiary_type
 * @property string $name
 * @property string $category
 * @property string $transaction_type
 * @property string $currency
 * @property array $bank_data
 * @property bool $enabled
 * @property mixed $links
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class BankCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($bank) {
            $data = [
                'id' => $bank->getKey() ?? null,
                'country_id' => $bank->country_id ?? null,
                'country_name' => null,
                'beneficiary_types' => ($bank->beneficiaryTypes) ? $bank->beneficiaryTypes->toArray() : [],
                'name' => $bank->name ?? null,
                'slug' => $bank->slug ?? null,
                'category' => $bank->category ?? null,
                'transaction_type' => $bank->transaction_type ?? null,
                'currency' => $bank->currency ?? null,
                'vendor_code' => $bank->vendor_code ?? (object) [],
                'bank_data' => $bank->bank_data ?? null,
                'logo_png' => $bank->getFirstMediaUrl('logo_png') ?? null,
                'logo_svg' => $bank->getFirstMediaUrl('logo_svg') ?? null,
                'enabled' => $bank->enabled ?? null,
                'created_at' => $bank->created_at,
                'updated_at' => $bank->updated_at,
            ];

            if (Core::packageExists('MetaData')) {
                $data['country_name'] = ($bank->country) ? $bank->country->name : null;
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
