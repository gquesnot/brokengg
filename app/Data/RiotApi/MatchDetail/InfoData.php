<?php

namespace App\Data\RiotApi\MatchDetail;

use App\Data\IntCast;
use App\Data\IntTransformer;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class InfoData extends \Spatie\LaravelData\Data
{
    public function __construct(
        #[WithCast(IntCast::class)]
        public int $game_creation,
        public int $game_duration,
        #[WithCast(IntCast::class)]
        public int $game_end_timestamp,
        #[WithCast(IntCast::class)]
        public int $game_id,
        public string $game_mode,
        public string $game_name,
        #[WithCast(IntCast::class)]
        public string $game_start_timestamp,
        public string $game_type,
        public string $game_version,
        public int $map_id,
        #[DataCollectionOf(ParticipantData::class)]
        public DataCollection $participants,
        public string $platform_id,
        public string $queue_id,
        #[DataCollectionOf(TeamData::class)]
        public DataCollection $teams,
        public string $tournament_code,
    )
    {
    }


}
