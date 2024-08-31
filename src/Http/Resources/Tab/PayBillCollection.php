<?php

namespace Fintech\RestApi\Http\Resources\Tab;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PayBillCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($pay_bill) {
            $data = [
                'id' => $pay_bill->getKey(),
                'source_country_id' => $pay_bill->source_country_id ?? null,
                'source_country_name' => null,
                'destination_country_id' => $pay_bill->destination_country_id ?? null,
                'destination_country_name' => null,
                'parent_id' => $pay_bill->parent_id ?? null,
                'sender_receiver_id' => $pay_bill->sender_receiver_id ?? null,
                'sender_receiver_name' => null,
                'user_id' => $pay_bill->user_id ?? null,
                'user_name' => null,
                'service_id' => $pay_bill->service_id ?? null,
                'service_name' => null,
                'service_type' => null,
                'transaction_form_id' => $pay_bill->transaction_form_id ?? null,
                'transaction_form_name' => $pay_bill->transaction_form_name ?? null,
                'ordered_at' => $pay_bill->ordered_at ?? null,
                'amount' => $pay_bill->amount ?? null,
                'currency' => $pay_bill->currency ?? null,
                'converted_amount' => $pay_bill->converted_amount ?? null,
                'converted_currency' => $pay_bill->converted_currency ?? null,
                'order_number' => $pay_bill->order_number ?? null,
                'risk' => $pay_bill->risk ?? null,
                'notes' => $pay_bill->notes ?? null,
                'is_refunded' => $pay_bill->is_refunded ?? null,
                'order_data' => $pay_bill->order_data ?? null,
                'status' => $pay_bill->status ?? null,
            ];

            if (Core::packageExists('MetaData')) {
                $data['source_country_name'] = $pay_bill->sourceCountry?->name ?? null;
                $data['destination_country_name'] = $pay_bill->destinationCountry?->name ?? null;
            }

            if (Core::packageExists('Auth')) {
                $data['sender_receiver_name'] = $pay_bill->senderReceiver?->name ?? null;
                $data['user_name'] = $pay_bill->user?->name ?? null;
            }

            if (Core::packageExists('Business')) {
                $data['service_name'] = $pay_bill->service?->service_name ?? null;
                $data['service_type'] = $pay_bill->service->serviceType?->all_parent_list ?? null;
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
