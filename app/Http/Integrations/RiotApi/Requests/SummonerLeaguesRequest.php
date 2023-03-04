<?php

namespace App\Http\Integrations\RiotApi\Requests;

use App\Data\RiotApi\LiveGameData;
use App\Data\RiotApi\SummonerData;
use App\Data\RiotApi\SummonerLeagueData;
use App\Enums\RiotApiPlatform;
use App\Http\Integrations\RiotApi\RiotPlatformApiConnector;
use App\Traits\HasDto;
use Illuminate\Testing\Fluent\Concerns\Has;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;
use Spatie\LaravelData\DataCollection;

class SummonerLeaguesRequest extends RiotRequest
{
    use HasConnector;
    protected string  $connector = RiotPlatformApiConnector::class;

    public function __construct(
        public string $summoner_id,
        RiotApiPlatform $platform = RiotApiPlatform::EUW1,
    )
    {
        parent::__construct($platform);
    }


    /**
     * Define the HTTP method
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    /**
     * Define the endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return '/lol/league/v4/entries/by-summoner/' . $this->summoner_id;
    }


    public function createDtoFromResponse(\Saloon\Contracts\Response  $response):DataCollection {
        return SummonerLeagueData::fromResponse($response);
    }

}
