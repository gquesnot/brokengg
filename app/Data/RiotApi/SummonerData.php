<?php

namespace App\Data\RiotApi;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class SummonerData extends \Spatie\LaravelData\Data
{

    public function __construct(
        public string $account_id,
        public int $profile_icon_id,
        public string $revision_date,
        public string $name,
        public string $id,
        public string $puuid,
        public int $summoner_level,
    )
    {
    }


    static function fromResponse(\Saloon\Contracts\Response $response): static
    {
        return self::from($response->json());
    }


}
