<?php

namespace App\Traits;

trait JsonCastTrait
{
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return self::from(json_decode($value, true));
    }

    public function set($model, $key, $value, $attributes): ?string
    {
        if ($value === null) {
            return null;
        }
        $clean_class = self::from([])->toArray();
        $keys = array_keys($clean_class);
        $value = $value->toArray();
        foreach ($keys as $key) {
            if ($value[$key] === $clean_class[$key]) {
                unset($value[$key]);
            }
        }

        return json_encode($value);
    }
}
