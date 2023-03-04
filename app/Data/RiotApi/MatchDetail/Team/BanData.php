<?php

namespace App\Data\RiotApi\MatchDetail\Team;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class BanData extends \Spatie\LaravelData\Data
{
    public function __construct(
        public int $champion_id,
        public int $pick_turn
    )
    {
    }
}
