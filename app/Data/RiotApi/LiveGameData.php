<?php

namespace App\Data\RiotApi;

use App\Data\RiotApi\LiveGame\BannedChampionData;
use App\Data\RiotApi\LiveGame\CurrentGameParticipantData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class LiveGameData extends \Spatie\LaravelData\Data
{

    public function __construct(
        public int $game_id,
        public string $game_type,
        public int $map_id,
        public int $game_length,
        public int $game_start_time,
        public int $platform_id,
        public int $game_mode,
        #[DataCollectionOf(BannedChampionData::class)]
        public DataCollection $banned_champions,
        #[DataCollectionOf(CurrentGameParticipantData::class)]
        public DataCollection $participants,
        #[MapInputName('observers.encryptionKey')]
        public string $observers_key,
        public int $game_queue_config_id,

    )
    {
    }

    static function fromResponse(\Saloon\Contracts\Response $response): static
    {
        return self::from($response->json());
    }

}
