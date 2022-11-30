<?php

namespace App\Casts;

use App\Data\DataCollectionJsonCast;
use Illuminate\Support\Collection;

class CastableCollectionJsonData
{
    public function __construct(
        /** @var class-string<DataCollectionJsonCast> $dataClass */
        protected string $dataClass
    ) {
    }

    public function get($model, $key, $value, $attributes): ?Collection
    {
        if ($value === null) {
            return null;
        }

        $payload = json_decode($value, true, flags: JSON_THROW_ON_ERROR);

        return collect($payload)->map(function ($item) {
            return ($this->dataClass)::from_model($item);
        });
    }

    public function set($model, $key, $value, $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode($value);
    }
}
