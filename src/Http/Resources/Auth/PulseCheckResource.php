<?php

namespace Fintech\RestApi\Http\Resources\Auth;

use Fintech\MetaData\Facades\MetaData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PulseCheckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = match (config('fintech.auth.geoip.default')) {
            'ipapi' => $this->ipapi($request),
            'ipinfo' => $this->ipinfo($request),
            'ipdata' => $this->ipdata($request),
            'ip2location' => $this->ip2location($request),
            'cloudflare' => $this->cloudflare($request),
            'kloudend' => $this->kloudend($request),
            'maxmind' => $this->maxmind($request),
            'local' => $this->local($request),
            default => $this->development($request),
        };

        $data['location']['country']['id'] = $data['country']?->id ?? null;
        $data['location']['country']['name'] = $data['country']?->name ?? null;
        $data['location']['country']['code'] = $data['country']?->iso2 ?? null;
        $data['location']['country']['logo_png'] = $data['country']?->getFirstMediaUrl('logo_png') ?? null;
        $data['location']['country']['logo_svg'] = $data['country']?->getFirstMediaUrl('logo_svg') ?? null;

        $data['location']['state']['id'] = $data['state']?->id ?? null;
        $data['location']['state']['type'] = $data['state']?->type ?? null;
        $data['location']['state']['name'] = $data['state']?->name ?? null;

        $data['location']['city']['id'] = $data['city']?->id ?? null;
        $data['location']['city']['name'] = $data['city']?->name ?? null;

        $data['timezone'] = $data['country']?->timezones ?? [(object) []];
        $data['timezone'] = $data['timezone'][0] ?? (object) [];
        $data['language'] = $data['country']?->language ?? [];
        $data['calling_code'] = '+'.str_replace(['+', '-'], '', ($data['country']?->phone_code ?? ''));

        $data['currency']['id'] = $data['country']?->id ?? null;
        $data['currency']['code'] = $data['country']?->currency ?? null;
        $data['currency']['name'] = $data['country']?->currency_name ?? null;
        $data['currency']['symbol'] = $data['country']?->currency_symbol ?? null;

        $data['security']['allow_to_signup'] = in_array($data['location']['country']['id'], MetaData::country()->servingIds());
        $data['security']['allow_to_login'] = in_array($data['location']['country']['id'], MetaData::country()->servingIds());
        $data['security']['is_proxy'] = null;
        $data['security']['proxy_type'] = null;
        $data['security']['proxy_last_detected'] = null;
        $data['security']['proxy_level'] = null;
        $data['security']['is_crawler'] = false;
        $data['security']['crawler_name'] = null;
        $data['security']['crawler_type'] = null;
        $data['security']['is_tor'] = false;
        $data['security']['threat_level'] = 'low';
        $data['security']['threat_types'] = null;
        $data['security']['vpn_service'] = null;
        $data['security']['anonymizer_status'] = null;
        $data['security']['hosting_facility'] = false;

        unset($data['country'], $data['state'], $data['city']);

        return $data;
    }

    private function ipapi(Request $request): array
    {
        $country = (! empty($this->country_code)) ? MetaData::country()->findWhere(['iso2' => $this->country_code]) : null;
        $state = (! empty($country)) ? MetaData::state()->findWhere(['country_id' => $country->id, 'search' => $this->region_name]) : null;

        return [
            'ip' => $this->ip ?? null,
            'type' => $this->type ?? 'ipv4',
            'country' => $country ?? null,
            'state' => $state ?? null,
            'city' => $this->city ?? null,
            'zip' => $this->zip ?? null,
            'latitude' => $this->latitude ?? 0,
            'longitude' => $this->longitude ?? 0,
        ];
    }

    private function ipinfo(Request $request): array
    {
        return [];
    }

    private function ipdata(Request $request): array
    {
        return [];
    }

    private function ip2location(Request $request): array
    {
        return [];
    }

    private function cloudflare(Request $request): array
    {
        return [];
    }

    private function kloudend(Request $request): array
    {
        return [];
    }

    private function maxmind(Request $request): array
    {
        return [];
    }

    private function local(Request $request): array
    {
        $country = MetaData::country()->findWhere(['iso2' => 'BD']);
        $state = MetaData::state()->findWhere(['country_id' => $country->id, 'search' => 'Dhaka District']);
        $city = MetaData::city()->findWhere(['country_id' => $country->id, 'state_id' => $state->id, 'search' => 'Dhaka']);

        return [
            'ip' => $this->ip ?? null,
            'type' => $this->type ?? 'ipv4',
            'country' => $country ?? null,
            'state' => $state ?? null,
            'city' => $city ?? null,
            'zip' => $this->zip ?? null,
            'latitude' => $this->latitude ?? 0,
            'longitude' => $this->longitude ?? 0,
        ];
    }

    private function development(Request $request): array
    {
        $country = MetaData::country()->findWhere(['iso2' => 'BD', 'enabled' => true]);

        $state = MetaData::state()->findWhere([
            'country_id' => $country->id,
            'search' => 'Dhaka District',
            'enabled' => true,
        ]);

        $city = MetaData::city()->findWhere([
            'country_id' => $country->id,
            'state_id' => $state?->id ?? null,
            'search' => 'Dhaka',
            'enabled' => true,
        ]);

        return [
            'ip' => $this->ip ?? null,
            'type' => $this->type ?? 'ipv4',
            'country' => $country ?? null,
            'state' => $state ?? null,
            'city' => $city ?? null,
            'zip' => '1212',
            'latitude' => 23.787710189819336,
            'longitude' => 90.3798828125,
        ];
    }
}
