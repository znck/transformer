<?php

namespace Znck\Transformers\Serializers;

use League\Fractal\Serializer\ArraySerializer;

class EmbedSerializer extends ArraySerializer
{
    public function null() {
        return [];
    }

    public function collection($resourceKey, array $data) {
        return [$resourceKey ?: 'items' => $data];
    }

    public function item($resourceKey, array $data) {
        return [$resourceKey ?: 'item' => $data];
    }

    public function meta(array $meta) {
        return ['meta' => $meta];
    }
}
