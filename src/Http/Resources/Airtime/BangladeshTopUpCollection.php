<?php

namespace Fintech\RestApi\Http\Resources\Airtime;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BangladeshTopUpCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($bdtopup) {
            $data = [
                'id' => $bdtopup->getKey(),
                'source_country_id' => $bdtopup->source_country_id ?? null,
                'source_country_name' => null,
                'destination_country_id' => $bdtopup->destination_country_id ?? null,
                'destination_country_name' => null,
                'parent_id' => $bdtopup->parent_id ?? null,
                'sender_receiver_id' => $bdtopup->sender_receiver_id ?? null,
                'sender_receiver_name' => null,
                'user_id' => $bdtopup->user_id ?? null,
                'user_name' => null,
                'service_id' => $bdtopup->service_id ?? null,
                'service_name' => null,
                'service_type' => null,
                'transaction_form_id' => $bdtopup->transaction_form_id ?? null,
                'transaction_form_name' => $bdtopup->transaction_form_name ?? null,
                'ordered_at' => $bdtopup->ordered_at ?? null,
                'amount' => $bdtopup->amount ?? null,
                'currency' => $bdtopup->currency ?? null,
                'converted_amount' => $bdtopup->converted_amount ?? null,
                'converted_currency' => $bdtopup->converted_currency ?? null,
                'order_number' => $bdtopup->order_number ?? null,
                'risk' => $bdtopup->risk ?? null,
                'notes' => $bdtopup->notes ?? null,
                'is_refunded' => $bdtopup->is_refunded ?? null,
                'order_data' => $bdtopup->order_data ?? null,
                'status' => $bdtopup->status ?? null,
            ];

            if (Core::packageExists('MetaData')) {
                $data['source_country_name'] = $bdtopup->sourceCountry?->name ?? null;
                $data['destination_country_name'] = $bdtopup->destinationCountry?->name ?? null;
            }

            if (Core::packageExists('Auth')) {
                $data['sender_receiver_name'] = $bdtopup->senderReceiver?->name ?? null;
                $data['user_name'] = $bdtopup->user?->name ?? null;
            }

            if (Core::packageExists('Business')) {
                $data['service_name'] = $bdtopup->service?->service_name ?? null;
                $data['service_type'] = $bdtopup->service->serviceType?->all_parent_list ?? null;
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
