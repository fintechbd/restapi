<?php

namespace Fintech\RestApi\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait IdDocTypeResourceTrait
{
    private function formatMediaCollection($collection = null): array
    {
        $data = [];

        if ($collection != null) {
            $collection->each(function (Media $media) use (&$data) {
                $data[$media->getCustomProperty('type')][$media->getCustomProperty('side')] = $media->getFullUrl();
            });
        }

        return $data;
    }
}
