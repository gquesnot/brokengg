<?php

namespace App\Data\RiotApi\SummonerLeague;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class MiniSeries extends \Spatie\LaravelData\Data
{

    public function __construct(
        public int $losses,
        public string $progress,
        public int $target,
        public int $wins,
    )
    {
    }


}
