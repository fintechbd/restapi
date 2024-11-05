<?php

namespace Fintech\RestApi\Http\Resources\Transaction;

use Fintech\Core\Enums\RequestPlatform;
use Fintech\Core\Facades\Core;
use Fintech\Core\Traits\CompliancePolicyTable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class TrackOrderResource extends JsonResource
{
    use CompliancePolicyTable;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $order_data = $this->order_data ?? [];

        Arr::forget($order_data, $this->hidden);

        $data = [
            'id' => $this->getKey(),
            'description' => $this->description ?? null,
            'source_country_name' => null,
            'destination_country_name' => null,
            'sender_receiver_name' => null,
            'user_name' => null,
            'service_name' => null,
            'service_type' => null,
            'transaction_form_name' => $this->transaction_form_name ?? null,
            'ordered_at' => $this->ordered_at ?? null,
            'amount' => $this->amount ?? null,
            'amount_formatted' => (string) \currency($this->amount ?? null, $this->currency),
            'currency' => $this->currency ?? null,
            'converted_amount' => $this->converted_amount ?? null,
            'converted_amount_formatted' => (string) \currency($this->converted_amount ?? null, $this->converted_currency),
            'converted_currency' => $this->converted_currency ?? null,
            'charge_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['charge_amount'] ?? null, $this->currency),
            'discount_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['discount_amount'] ?? null, $this->currency),
            'commission_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['commission_amount'] ?? null, $this->currency),
            'cost_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['cost_amount'] ?? null, $this->currency),
            'total_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['total_amount'] ?? null, $this->currency),
            'order_number' => $this->order_number ?? null,
            'risk_profile' => $this->risk_profile ?? null,
            'notes' => $this->notes,
            'timeline' => $this->timeline ?? null,
            'is_refunded' => $this->is_refunded ?? null,
            'order_data' => $order_data ?? null,
            'status' => $this->status ?? null,
            'request_platform' => RequestPlatform::tryFrom($this->order_data['request_from']),
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];

        if (Core::packageExists('MetaData')) {
            $data['source_country_name'] = $this->sourceCountry?->name ?? null;
            $data['destination_country_name'] = $this->destinationCountry?->name ?? null;
        }

        if (Core::packageExists('Auth')) {
            $data['sender_receiver_name'] = $this->senderReceiver?->name ?? null;
            $data['user_name'] = $this->user?->name ?? null;
        }

        if (Core::packageExists('Business')) {
            $data['service_name'] = $this->service?->service_name ?? null;
            $data['service_type'] = $order->service->serviceType?->all_parent_list ?? [];
        }

        $this->renderPolicyData($data['order_data']);

        if (empty($data['order_data']['compliance_data'])) {
            unset($data['order_data']['compliance_data']);
        }

        return $data;
    }

    private $hidden = [
        'role_id',
        'is_reload',
        'is_reverse',
        'service_stat_data',
        'assign_order',
        'fund_source',
        'remittance_purpose',
        'beneficiary_type_id',
        'system_notification_variable_failed',
        'system_notification_variable_success',
        'request_from',
        'wallet_id',
        'bank_id',
        'cash_pickup_id',
        'bank_branch_id',
        'created_at',
        'created_by',
        'beneficiary_id',
        'beneficiary_data.reference_no',
        'beneficiary_data.sender_information.profile',
        'beneficiary_data.sender_information.fcm_token',
        'beneficiary_data.sender_information.language',
        'beneficiary_data.sender_information.currency',
        'beneficiary_data.sender_information.profile',
        'beneficiary_data.receiver_information.city_id',
        'beneficiary_data.receiver_information.state_id',
        'beneficiary_data.receiver_information.country_id',
        'beneficiary_data.receiver_information.city_data',
        'beneficiary_data.receiver_information.state_data',
        'beneficiary_data.receiver_information.country_data',
        'beneficiary_data.receiver_information.relation_data',
        'beneficiary_data.receiver_information.beneficiary_type_id',
        'beneficiary_data.bank_information.bank_data',
        'beneficiary_data.bank_information.vendor_code',
        'beneficiary_data.branch_information.vendor_code',
        'beneficiary_data.wallet_information.vendor_code',
        'beneficiary_data.wallet_information.bank_data',
        'beneficiary_data.cash_pickup_information.vendor_code',
        'beneficiary_data.cash_pickup_information.bank_data',

    ];
}
