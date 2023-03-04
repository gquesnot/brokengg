<?php

namespace App\Http\Integrations\RiotApi\Requests;

use App\Data\RiotApi\MatchTimelineData;
use App\Data\RiotApi\SummonerData;
use App\Enums\RiotApiPlatform;
use App\Http\Integrations\RiotApi\RiotPlatformApiConnector;
use App\Http\Integrations\RiotApi\RiotRegionApiConnector;
use App\Traits\HasDto;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;

class MatchTimelineRequest extends RiotRequest
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
        return "/lol/match/v5/matches/{$this->match_id}/timeline";
    }
    public function createDtoFromResponse(\Saloon\Contracts\Response  $response):MatchTimelineData {
        return MatchTimelineData::fromResponse($response);
    }


}
