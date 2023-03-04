<?php

namespace App\Http\Integrations\RiotApi\Requests;

use App\Data\RiotApi\MatchDetailData;
use App\Data\RiotApi\SummonerData;
use App\Enums\RiotApiPlatform;
use App\Http\Integrations\RiotApi\RiotPlatformApiConnector;
use App\Http\Integrations\RiotApi\RiotRegionApiConnector;
use App\Traits\HasDto;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;

class MatchDetailRequest extends RiotRequest
{
    use HasConnector;
    /**
     * Define the HTTP method
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    protected string  $connector = RiotRegionApiConnector::class;

    public function __construct(
        public string $match_id,
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
        return '/lol/match/v5/matches/' . $this->match_id;
    }

    public function createDtoFromResponse(\Saloon\Contracts\Response  $response):MatchDetailData {
        return MatchDetailData::fromResponse($response);
    }

    public function defaultDelay(): ?int
    {
        return 1300; # 100 req  = 120s => 1 req = 1.2s
    }
}
