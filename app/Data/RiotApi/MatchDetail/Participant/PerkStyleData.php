<?php

namespace App\Data\RiotApi\MatchDetail\Participant;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class PerkStyleData extends \Spatie\LaravelData\Data
{

    public function __construct(
        public string         $description,
        #[DataCollectionOf(PerkStyleSelectionData::class)]
        public DataCollection $selections,
        public int            $style,
    )
    {
    }
}
