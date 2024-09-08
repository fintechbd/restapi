<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->resource = (object) $this->resource;

        return [
            'rate' => (string) ($this->rate ?? 1),

            'input' => $this->input,
            'input_unit' => $this->input_unit,

            'output' => $this->output,
            'output_unit' => $this->output_unit,
            //            "input_symbol" => $this->input_symbol,
            //            "output_symbol" => $this->output_symbol,

            'amount' => (string) $this->amount,
            'amount_formatted' => (string) \currency($this->amount, $this->input),

            'converted' => (string) $this->converted,
            'converted_formatted' => (string) \currency($this->converted, $this->output),

            'charge_amount' => (string) $this->charge_amount,
            'charge_amount_formatted' => (string) \currency($this->charge_amount, $this->base_currency),

            'discount_amount' => (string) $this->discount_amount,
            'discount_amount_formatted' => (string) \currency($this->discount_amount, $this->base_currency),

            'commission_amount' => (string) $this->commission_amount,
            'commission_amount_formatted' => (string) \currency($this->commission_amount, $this->base_currency),

            'total_amount' => (string) $this->total_amount,
            'total_amount_formatted' => (string) \currency($this->total_amount, $this->base_currency),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'options' => [],
            'meta' => [
                'charge' => $this->charge,
                'discount' => $this->discount,
                'commission' => $this->commission,
                'charge_break_down_data' => [
                    'id' => $this->charge_break_down_data['id'] ?? null,
                    'lower_limit' => (string) \currency($this->charge_break_down_data['lower_limit'], $this->base_currency),
                    'higher_limit' => (string) \currency($this->charge_break_down_data['higher_limit'], $this->base_currency),
                ],
            ],
            'query' => $request->all(),
        ];
    }
}
