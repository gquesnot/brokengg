<?php

namespace App\Enums;

enum DamageType: string
{
    case AD = 'MAGIC_DAMAGE';
    case AP = 'PHYSICAL_DAMAGE';
    case TRUE = 'TRUE_DAMAGE';
}
