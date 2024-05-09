<?php

namespace Fintech\RestApi\Http\Resources\Airtime;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InternationalTopUpCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($intltopup) {
            $data = [
                'id' => $intltopup->getKey(),
                'source_country_id' => $intltopup->source_country_id ?? null,
                'source_country_name' => null,
                'destination_country_id' => $intltopup->destination_country_id ?? null,
                'destination_country_name' => null,
                'parent_id' => $intltopup->parent_id ?? null,
                'sender_receiver_id' => $intltopup->sender_receiver_id ?? null,
                'sender_receiver_name' => null,
                'user_id' => $intltopup->user_id ?? null,
                'user_name' => null,
                'service_id' => $intltopup->service_id ?? null,
                'service_name' => null,
                'service_type' => null,
                'transaction_form_id' => $intltopup->transaction_form_id ?? null,
                'transaction_form_name' => $intltopup->transaction_form_name ?? null,
                'ordered_at' => $intltopup->ordered_at ?? null,
                'amount' => $intltopup->amount ?? null,
                'currency' => $intltopup->currency ?? null,
                'converted_amount' => $intltopup->converted_amount ?? null,
                'converted_currency' => $intltopup->converted_currency ?? null,
                'order_number' => $intltopup->order_number ?? null,
                'risk' => $intltopup->risk ?? null,
                'notes' => $intltopup->notes ?? null,
                'is_refunded' => $intltopup->is_refunded ?? null,
                'order_data' => $intltopup->order_data ?? null,
                'status' => $intltopup->status ?? null,
            ];

            if (Core::packageExists('MetaData')) {
                $data['source_country_name'] = $intltopup->sourceCountry?->name ?? null;
                $data['destination_country_name'] = $intltopup->destinationCountry?->name ?? null;
            }

            if (Core::packageExists('Auth')) {
                $data['sender_receiver_name'] = $intltopup->senderReceiver?->name ?? null;
                $data['user_name'] = $intltopup->user?->name ?? null;
            }

            if (Core::packageExists('Business')) {
                $data['service_name'] = $intltopup->service?->service_name ?? null;
                $data['service_type'] = $intltopup->service->serviceType?->all_parent_list ?? null;
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
