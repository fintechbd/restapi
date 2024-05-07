<?php

namespace Fintech\RestApi\Http\Resources\Promo;

use Fintech\Core\Facades\Core;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int $present_country_id
 * @property int $permanent_country_id
 * @property string $name
 * @property string $category
 * @property string $content
 * @property array $link
 * @property bool $enabled
 * @property array $promotion_data
 * @property mixed $presentCountry
 * @property mixed $permanentCountry
 *
 * @method getFirstMediaUrl(string $string)
 */
class PromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id ?? null,
            'present_country_id' => $this->present_country_id ?? null,
            'present_country_name' => null,
            'permanent_country_id' => $this->permanent_country_id ?? null,
            'permanent_country_name' => null,
            'name' => $this->name ?? null,
            'type' => $this->type ?? null,
            'content' => $this->content ?? null,
            'photo' => $this->getFirstMediaUrl('photo') ?? null,
            'enabled' => $this->enabled ?? false,
            'promotion_data' => $this->promotion_data ?? [],
        ];

        if (Core::packageExists('MetaData')) {
            $data['present_country_name'] = $this->presentCountry->name ?? null;
            $data['permanent_country_name'] = $this->permanentCountry->name ?? null;
        }

        return $data;
    }
}
