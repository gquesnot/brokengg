<?php

namespace App\Data\RiotApi\MatchDetail\Team;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class ObjectiveData extends \Spatie\LaravelData\Data
{

        public function __construct(
            public bool $first,
            public int $kills,
        )
        {
        }
}
