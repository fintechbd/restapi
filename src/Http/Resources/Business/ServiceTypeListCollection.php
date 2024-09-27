<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Business\Facades\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceTypeListCollection extends ResourceCollection
{
    private array $serviceTypeList = [];

    private array $defaultServiceSettings = [];

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        $this->prepareServiceTypeMetaData();

        return $this->collection->map(function ($item) {
            $entries = [];
            $this->loadServiceTypeParentList($entries, $item->service_type_parent_id);
            $parent = [
                'id' => null,
                'service_type_name' => '',
                'service_type_slug' => '',
            ];
            if ($item->service_type_parent_id != null && isset($this->serviceTypeList[$item->service_type_parent_id])) {
                $parent = $this->serviceTypeList[$item->service_type_parent_id];
            }

            return [
                'id' => $item->id,
                'logo_svg' => $item->logo_svg,
                'logo_png' => $item->logo_png,
                'service_type_parent_id' => $item->service_type_parent_id ?? null,
                'service_type_name' => $item->service_type_name ?? '',
                'service_type_is_parent' => $item->service_type_is_parent ?? 'no',
                'service_type_parent' => $parent,
                'service_type_parent_list' => array_reverse($entries),
                'service_type_slug' => $item->service_type_slug ?? '',
                'service_type_step' => $item->service_type_step ?? 1,
                'service_id' => $item->service_id ?? null,
                'service_name' => $item->service_name ?? '',
                'service_slug' => $item->service_slug ?? '',
                'service_data' => $this->configureServiceSettingData($item->service_data),
                'service_vendor_id' => $item->service_vendor_id ?? null,
                'service_vendor_name' => $item->service_vendor_name ?? '',
                'destination_country_id' => $item->destination_country_id ?? null,
                'source_country_id' => $item->source_country_id ?? null,
                'menu_position' => $item->service_serial ?? -1,
            ];
        })->toArray();
    }

    private function prepareServiceTypeMetaData(): void
    {
        if (! cache()->has('fintech.serviceTypeList')) {
            Business::serviceType()->list([
                'get' => [
                    'service_types.id',
                    'service_types.service_type_name',
                    'service_types.service_type_parent_id',
                    'service_types.service_type_slug',
                ],
                'paginate' => false,
                'sort' => 'service_types.id',
            ])
                ->each(fn ($serviceType) => $this->serviceTypeList[$serviceType->id] = $serviceType->toArray());
            cache()->put('fintech.serviceTypeList', $this->serviceTypeList, HOUR);
        } else {
            $this->serviceTypeList = cache()->get('fintech.serviceTypeList', []);
        }

        Business::serviceSetting()
            ->list(['enabled' => true, 'paginate' => false, 'service_setting_type' => 'service'])
            ->each(function ($item) {
                $this->defaultServiceSettings[$item->service_setting_field_name] = $item->service_setting_value ?? null;
                if (empty($this->defaultServiceSettings[$item->service_setting_field_name])) {
                    $this->defaultServiceSettings[$item->service_setting_field_name] = match ($item->service_setting_type_field) {
                        'text' => '',
                        default => null
                    };
                } else {
                    $this->defaultServiceSettings[$item->service_setting_field_name] = match ($item->service_setting_type_field) {
                        'text' => (string) $this->defaultServiceSettings[$item->service_setting_field_name],
                        'number' => (int) $this->defaultServiceSettings[$item->service_setting_field_name],
                        default => $this->defaultServiceSettings[$item->service_setting_field_name]
                    };
                }
            });
    }

    private function configureServiceSettingData($settings): array
    {
        if (empty($settings)) {
            $settings = [];
        }

        $settings = array_merge($this->defaultServiceSettings, $settings);

        foreach ($settings as $key => $value) {
            $settings[$key] = match ($key) {
                'beneficiary_type_id', 'amount_range', 'operator_short_code' => empty($value) ? null : (int) $value,
                'visible_website', 'visible_android_app', 'visible_ios_app' => empty($value) ? 'no' : $value,
                default => $value
            };
        }

        return $settings;
    }

    private function loadServiceTypeParentList(array &$collection, $parent_id = null): void
    {
        if ($parent_id != null) {

            $parent = $this->serviceTypeList[$parent_id] ?? null;

            if ($parent == null) {
                return;
            }

            $parent_id = $parent['service_type_parent_id'];

            unset($parent['service_type_parent_id']);

            $collection[] = $parent;

            $this->loadServiceTypeParentList($collection, $parent_id);
        }
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'total' => $this->collection?->count() ?? 0,
            'options' => [],
            'query' => $request->all(),
        ];
    }
}
