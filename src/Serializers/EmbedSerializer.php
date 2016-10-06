<?php namespace Znck\Transformers\Serializers;

use League\Fractal\Serializer\ArraySerializer;

class EmbedSerializer extends ArraySerializer
{
    public function null() {
        return [];
    }

    public function meta(array $meta) {
        if (empty($meta)) {
            return [];
        }

        return ['_meta' => $meta];
    }
}
