<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum Rank: string
{
    use EnumTrait;

    case I = 'I';
    case II = 'II';
    case III = 'III';
    case IV = 'IV';

    public function number(Tier $tier): int
    {
        if ($tier->number() >= 25) { // master
            return 0;
        }

        return match ($this) {
            self::IV => 0,
            self::III => 1,
            self::II => 2,
            self::I => 3,
        };
    }

    public static function rankFromNumber(int $number): Rank
    {
        if ($number >= 25) { // master
            return self::I;
        }

        $number = $number % 4;

        return match ($number) {
            0 => self::IV,
            1 => self::III,
            2 => self::II,
            3 => self::I,
        };
    }
}
