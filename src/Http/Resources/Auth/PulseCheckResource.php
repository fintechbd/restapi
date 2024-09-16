<?php

namespace Fintech\RestApi\Http\Resources\Auth;

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
        return match (config('fintech.auth.geoip.default')) {
            'ipapi' => $this->ipapi($request),
            'ipinfo' => $this->ipinfo($request),
            'ipdata' => $this->ipdata($request),
            'ip2location' => $this->ip2location($request),
            'cloudflare' => $this->cloudflare($request),
            'kloudend' => $this->kloudend($request),
            'maxmind' => $this->maxmind($request),
            default => $this->local($request)
        };
    }

    private function ipapi(Request $request): array
    {
        return [
            "ip" => $this->ip ?? null,
            "type" => $this->type ?? "ipv4",
            "continent_code" => $this->continent_code ?? null,
            "continent_name" => $this->continent_name ?? null,
            "country_code" => $this->country_code ?? null,
            "country_name" => $this->country_name ?? null,
            "region_code" => $this->region_code ?? null,
            "region_name" => $this->region_name ?? null,
            "city" => $this->city ?? null,
            "zip" => $this->zip ?? null,
            "latitude" => $this->latitude ?? 0,
            "longitude" => $this->longitude ?? 0,
            "location" => [
                "capital" => $this->location['capital'] ?? null,
                "languages" => [
                    [
                        "code" => $this->location['languages'][0]['code'] ?? null,
                        "name" => $this->location['languages'][0]['name'] ?? null,
                        "native" => $this->location['languages'][0]['native'] ?? null,
                    ]
                ],
                "country_flag" => $this->location['country_flag'] ?? '#',
                "country_flag_emoji" => $this->location['country_flag_emoji'] ?? '#',
                "country_flag_emoji_unicode" => $this->location['country_flag_emoji_unicode'] ?? '#',
                "calling_code" =>'+' . str_replace(["+", "-"], '', ($this->location['calling_code'] ?? '')),
                "is_eu" => $this->location['is_eu'] ?? false
            ],
            "time_zone" => [
                "id" => $this->time_zone['id'] ?? "",
                "current_time" => $this->time_zone['current_time'] ?? "",
                "gmt_offset" => $this->time_zone[''] ?? 0,
                "code" => $this->time_zone['code'] ?? "0",
                "is_daylight_saving" => $this->time_zone['is_daylight_saving'] ?? false
            ],
            "currency" => [
                "code" => $this->currency["code"] ?? "BDT",
            ],
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
        return [];
    }
}
