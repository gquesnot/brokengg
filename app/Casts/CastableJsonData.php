<?php

namespace App\Casts;

use App\Data\DataJsonCast;

class CastableJsonData
{

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

        return ($this->dataClass)::withoutMagicalCreationFrom($payload);
    }


    public function set($model, $key, $value, $attributes): ?string
    {
        if ($value === null) {
            return null;
        }
        $transformed = $value->transform();
        // remove 0 values
        $transformed = array_filter($transformed, function ($value) {
            return $value !== 0 && $value != 0.0;
        });
        if (count($transformed) === 0) {
            return null;
        }
        return json_encode($transformed);
    }
}
