<?php

namespace App\Http\Integrations\RiotApi\Requests;

use App\Data\RiotApi\LiveGameData;
use App\Enums\RiotApiPlatform;
use App\Http\Integrations\RiotApi\RiotPlatformApiConnector;
use App\Models\Summoner;
use App\Traits\HasDto;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;
use Spatie\LaravelData\DataCollection;

class LiveGameRequest extends RiotRequest
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
        public string $summoner_id,
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
        return '/lol/spectator/v4/active-games/by-summoner/' . $this->summoner_id;
    }

    public function createDtoFromResponse(\Saloon\Contracts\Response  $response):LiveGameData {
        return LiveGameData::fromResponse($response);
    }
}
