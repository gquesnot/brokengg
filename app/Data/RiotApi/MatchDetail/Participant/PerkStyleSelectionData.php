<?php

namespace App\Data\RiotApi\MatchDetail\Participant;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class PerkStyleSelectionData extends \Spatie\LaravelData\Data
{

    public function __construct(
        public int $perk,
        public int $var1,
        public int $var2,
        public int $var3,
    )
    {
    }
}
