<?php

namespace App\Http\Integrations\RiotApi\Requests;

use App\Data\RiotApi\SummonerData;
use App\Enums\RiotApiPlatform;
use App\Http\Integrations\RiotApi\RiotPlatformApiConnector;
use App\Traits\HasDto;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;

class SummonerByNameRequest extends RiotRequest
{

    use HasConnector;

    /**
     * Define the HTTP method
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    protected string  $connector = RiotPlatformApiConnector::class;

    public function __construct(
        public string $summoner_name,
        RiotApiPlatform $platform = RiotApiPlatform::EUW1,
    )
    {
        parent::__construct($platform);
    }

    /**
     * Define the endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return '/lol/summoner/v4/summoners/by-name/' . $this->summoner_name;
    }

    public function createDtoFromResponse(\Saloon\Contracts\Response  $response):SummonerData {
        return SummonerData::fromResponse($response);
    }
}
