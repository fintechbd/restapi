<?php

namespace Fintech\RestApi\Http\Resources\Business;

use Fintech\Business\Facades\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceTypeListCollection extends ResourceCollection
{
    private array $serviceTypeList = [];

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        if (!cache()->has('fintech.serviceTypeList')) {
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
                ->each(fn($serviceType) => $this->serviceTypeList[$serviceType->id] = $serviceType->toArray());
            cache()->put('fintech.serviceTypeList', $this->serviceTypeList, HOUR);
        } else {
            $this->serviceTypeList = cache()->get('fintech.serviceTypeList', []);
        }

        Business::serviceSetting()
            ->list(['enabled' => true, 'paginate' => false, 'service_setting_type' => 'service'])
            ->each(function ($item) use (&$settings) {
                $settings[$item->service_setting_field_name] = $item->service_setting_value ?? null;
                if (is_null($settings[$item->service_setting_field_name])) {
                    if ($item->service_setting_type_field == 'text') {
                        $settings[$item->service_setting_field_name] = '';
                    }
                }
            });

        $services = $this->collection->map(function ($item) use ($request, $settings) {

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
                'service_data' => array_merge($settings, ($item->service_data ?? [])),
                'service_vendor_id' => $item->service_vendor_id ?? null,
                'service_vendor_name' => $item->service_vendor_name ?? '',
                'destination_country_id' => $item->destination_country_id ?? $request->integer('destination_country_id'),
                'source_country_id' => $item->source_country_id ?? $request->integer('source_country_id'),
                'menu_position' => $item->service_serial ?? -1,
            ];
        });

        return $services->toArray();
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
