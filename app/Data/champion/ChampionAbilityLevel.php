<?php

namespace App\Data\champion;

use App\Traits\JsonCastTrait;

class ChampionAbilityLevel
{
    use JsonCastTrait;

    public int $ap_ratio = 0;

    public int $ad_ratio = 0;

    public int $base_ad = 0;

    public int $base_ap = 0;

    public int $cdr = 0;

    public int $cost = 0;
}
