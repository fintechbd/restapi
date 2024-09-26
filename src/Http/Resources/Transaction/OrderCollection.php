<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Enums\Transaction\OrderType;
use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function currency;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($order) {
            $data = [
                'id' => $order->getKey(),
                'source_country_id' => $order->source_country_id ?? null,
                'source_country_name' => null,
                'destination_country_id' => $order->destination_country_id ?? null,
                'destination_country_name' => null,
                'parent_id' => $order->parent_id ?? null,
                'sender_receiver_id' => $order->sender_receiver_id ?? null,
                'sender_receiver_name' => null,
                'user_id' => $order->user_id ?? null,
                'user_name' => null,
                'service_id' => $order->service_id ?? null,
                'service_name' => null,
                'service_type' => null,
                'transaction_form_id' => $order->transaction_form_id ?? null,
                'transaction_form_name' => $order->transaction_form_name ?? null,
                'ordered_at' => $order->ordered_at ?? null,
                'amount' => $order->amount ?? null,
                'currency' => $order->currency ?? null,
                'converted_amount' => $order->converted_amount ?? null,
                'converted_currency' => $order->converted_currency ?? null,
                'order_number' => $order->order_number ?? null,
                'risk' => $order->risk ?? null,
                'notes' => $order->notes ?? null,
                'is_refunded' => $order->is_refunded ?? null,
                'order_data' => $order->order_data ?? null,
                'status' => $order->status ?? null,
            ];

            $data['amount_formatted'] = (string) currency($data['amount'], $data['currency']);
            $data['converted_amount_formatted'] = (string) currency($data['converted_amount'], $data['converted_currency']);

            if (Core::packageExists('MetaData')) {
                $data['source_country_name'] = $order->sourceCountry?->name ?? null;
                $data['destination_country_name'] = $order->destinationCountry?->name ?? null;
            }

            if (Core::packageExists('Auth')) {
                $data['sender_receiver_name'] = $order->senderReceiver?->name ?? null;
                $data['user_name'] = $order->user?->name ?? null;
            }

            if (Core::packageExists('Business')) {
                $data['service_name'] = $order->service?->service_name ?? null;
                $data['service_logo_png'] = $order->service?->getFirstMediaUrl('logo_png') ?? null;
                $data['service_logo_svg'] = $order->service?->getFirstMediaUrl('logo_svg') ?? null;
                $data['service_type'] = $order->service->serviceType?->all_parent_list ?? null;
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
                'order_type' => OrderType::values(),
            ],
            'query' => $request->all(),
        ];
    }
}
