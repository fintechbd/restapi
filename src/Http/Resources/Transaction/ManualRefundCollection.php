<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ManualRefundCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($refund) {
            $data = [
                'id' => $refund->getKey(),
                'source_country_id' => $refund->source_country_id ?? null,
                'source_country_name' => null,
                'destination_country_id' => $refund->destination_country_id ?? null,
                'destination_country_name' => null,
                'parent_id' => $refund->parent_id ?? null,
                'sender_receiver_id' => $refund->sender_receiver_id ?? null,
                'sender_receiver_name' => null,
                'user_id' => $refund->user_id ?? null,
                'user_name' => null,
                'service_id' => $refund->service_id ?? null,
                'service_name' => null,
                'service_type' => null,
                'transaction_form_id' => $refund->transaction_form_id ?? null,
                'transaction_form_name' => $refund->transaction_form_name ?? null,
                'ordered_at' => $refund->ordered_at ?? null,
                'amount' => $refund->amount ?? null,
                'currency' => $refund->currency ?? null,
                'converted_amount' => $refund->converted_amount ?? null,
                'converted_currency' => $refund->converted_currency ?? null,
                'order_number' => $refund->order_number ?? null,
                'risk' => $refund->risk ?? null,
                'notes' => $refund->notes ?? null,
                'is_refunded' => $refund->is_refunded ?? null,
                'order_data' => $refund->order_data ?? null,
                'status' => $refund->status ?? null,
            ];

            if (Core::packageExists('MetaData')) {
                $data['source_country_name'] = $refund->sourceCountry?->name ?? null;
                $data['destination_country_name'] = $refund->destinationCountry?->name ?? null;
            }

            if (Core::packageExists('Auth')) {
                $data['sender_receiver_name'] = $refund->senderReceiver?->name ?? null;
                $data['user_name'] = $refund->user?->name ?? null;
            }

            if (Core::packageExists('Business')) {
                $data['service_name'] = $refund->service?->service_name ?? null;
                $data['service_type'] = $refund->service->serviceType?->all_parent_list ?? null;
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
