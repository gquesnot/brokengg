<?php

namespace App\Traits;

trait EnumTrait
{
    public static function values(): array
    {
        $values = [];
        foreach (self::cases() as $case) {
            $values[] = $case->value;
        }
        //Log::debug('EnumTrait::values() : ', $values);

        return $values;
    }
}
