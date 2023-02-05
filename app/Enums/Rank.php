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

    public function number(): int
    {
        return match ($this) {
            self::I => 1,
            self::II => 2,
            self::III => 3,
            self::IV => 4,
        };
    }
}
