<?php

namespace App\Traits;

use App\Data\DataJsonCast;

trait CastableJsonData
{
    use WireableData;

    public function __construct(
        /** @var class-string<DataJsonCast> $dataClass */
        protected string $dataClass
    ) {
    }



    public function get($model, $key, $value, $attributes): ?DataJsonCast
    {
        if ($value === null) {
            return null;
        }

        $payload = json_decode($value, true, flags: JSON_THROW_ON_ERROR);

        return ($this->dataClass)::from($payload);
    }


    public function set($model, $key, $value, $attributes): ?string
    {
        if ($value === null) {
            return null;
        }
        return $value->toJson();
    }
}
