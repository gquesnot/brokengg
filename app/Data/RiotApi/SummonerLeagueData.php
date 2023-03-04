<?php

namespace App\Data\RiotApi;

use App\Data\RiotApi\SummonerLeague\MiniSeries;
use App\Enums\Rank;
use App\Enums\RankedType;
use App\Enums\Tier;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class SummonerLeagueData extends \Spatie\LaravelData\Data
{

    public RankedType $type;
    public function __construct(
        public string $league_id,
        public string $summoner_id,
        public string $summoner_name,
        public string $queue_type,
        public Tier $tier,
        public Rank $rank,
        public int $league_points,
        public int $wins,
        public int $losses,
        public bool $veteran,
        public bool $inactive,
        public bool $hot_streak,
        #public ?MiniSeries $mini_series=null,
    )
    {
        $this->type = $this->queue_type == 'RANKED_SOLO_5x5' ? RankedType::SOLO : RankedType::FLEX;
    }

    static function fromResponse(\Saloon\Contracts\Response $response): DataCollection
    {
        return self::collection($response->json());
    }


}
