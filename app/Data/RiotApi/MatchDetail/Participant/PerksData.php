<?php

namespace App\Data\RiotApi\MatchDetail\Participant;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class PerksData extends \Spatie\LaravelData\Data
{

        public function __construct(
            public PerkStatsData  $stat_perks,
            #[DataCollectionOf(PerkStyleData::class)]
            public DataCollection $styles,
        )
        {
        }
}
