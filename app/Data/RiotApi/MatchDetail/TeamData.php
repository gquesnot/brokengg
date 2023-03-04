<?php

namespace App\Data\RiotApi\MatchDetail;

use App\Data\RiotApi\MatchDetail\Team\BanData;
use App\Data\RiotApi\MatchDetail\Team\ObjectivesData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class TeamData extends \Spatie\LaravelData\Data
{
    public function __construct(
        #[DataCollectionOf(BanData::class)]
        public DataCollection $bans,
        public ObjectivesData $objectives,
        public int            $team_id,
        public bool           $win
    )
    {
    }
}
