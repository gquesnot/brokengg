<?php

namespace App\Helpers;

use App\Enums\Rank;
use App\Enums\Tier;

class LeagueHelper
{
    public static function getLeagueFromNumber(int $number): string
    {
        return Tier::tierFromNumber($number)->name() . " " . Rank::rankFromNumber($number)->value;
    }
}
