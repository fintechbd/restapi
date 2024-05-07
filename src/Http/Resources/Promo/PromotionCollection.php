<?php

namespace Fintech\RestApi\Http\Resources\Promo;

use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Fintech\MetaData\Facades\MetaData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use stdClass;

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
class PromotionCollection extends ResourceCollection
{
    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        $countries = new stdClass();

        if (Core::packageExists('MetaData')) {
            $countries = MetaData::country()->list(['enabled' => true])->pluck('name', 'id')->toArray();
        }

        return [
            'options' => [
                'dir' => Constant::SORT_DIRECTIONS,
                'per_page' => Constant::PAGINATE_LENGTHS,
                'sort' => ['id', 'name', 'category', 'created_at', 'updated_at'],
                'country_id' => $countries,
                'type' => config('fintech.promo.promotion_types', []),
            ],
            'query' => $request->all(),
        ];
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): mixed
    {
        return $this->collection->map(function ($promotion) {
            $data = [
                'id' => $promotion->id ?? null,
                'present_country_id' => $promotion->present_country_id ?? null,
                'present_country_name' => null,
                'permanent_country_id' => $promotion->permanent_country_id ?? null,
                'permanent_country_name' => null,
                'name' => $promotion->name ?? null,
                'type' => $promotion->type ?? null,
                'content' => $promotion->content ?? null,
                'photo' => $promotion->getFirstMediaUrl('photo') ?? null,
                'enabled' => $promotion->enabled ?? false,
                'promotion_data' => $promotion->promotion_data ?? [],
            ];

            if (Core::packageExists('MetaData')) {
                $promotion->load(['presentCountry', 'permanentCountry']);
                $data['present_country_name'] = $promotion->presentCountry->name;
                $data['permanent_country_name'] = $promotion->permanentCountry->name;
            }

            return $data;

        })->toArray();
    }
}
