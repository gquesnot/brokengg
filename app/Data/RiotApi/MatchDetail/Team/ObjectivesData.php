<?php

namespace App\Data\RiotApi\MatchDetail\Team;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class ObjectivesData extends \Spatie\LaravelData\Data
{
    public function __construct(
        public ObjectiveData $baron,
        public ObjectiveData $champion,
        public ObjectiveData $dragon,
        public ObjectiveData $inhibitor,
        public ObjectiveData $rift_herald,
        public ObjectiveData $tower,
    )
    {
    }
}
