<?php

namespace App\Data\RiotApi\LiveGame;

use App\Data\RiotApi\MatchDetail\Participant\PerksData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class CurrentGameParticipantData extends  Data
{

    public function __construct(
        public int                   $champion_id,
        #public Perks $perks,
        public int                   $profile_icon_id,
        public bool                  $bot,
        public int                   $team_id,
        public string                $summoner_name,
        public string                $summoner_id,
        public int                   $spell1_id,
        public int                   $spell2_id,
        public GameCustomizationData $game_customization_objects,
    )
    {
    }
}
