<?php

namespace App\Data\RiotApi\MatchDetail\Participant;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class PerkStatsData extends \Spatie\LaravelData\Data
{

        public function __construct(
            public int $defense,
            public int $flex,
            public int $offense,
        )
        {
        }
}
