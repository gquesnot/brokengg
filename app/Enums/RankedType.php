<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum RankedType: string
{
    use EnumTrait;

    case SOLO = 'solo';
    case FLEX = 'flex';
}
