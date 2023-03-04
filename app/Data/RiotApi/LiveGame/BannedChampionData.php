<?php

namespace App\Data\RiotApi\LiveGame;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class BannedChampionData extends Data
{
    public function __construct(
        public int $champion_id,
        public int $team_id,
        public int $pick_turn,
    )
    {
    }
}
