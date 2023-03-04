<?php

namespace App\Http\Integrations\RiotApi;

use App\Enums\RiotApiPlatform;
use Saloon\Contracts\HasPagination;
use Saloon\Contracts\Paginator;
use Saloon\Contracts\Request;
use Saloon\Contracts\Response;
use Saloon\Http\Connector;
use Saloon\Http\Paginators\PagedPaginator;
use Saloon\Traits\Plugins\AcceptsJson;
use Throwable;

class RiotApiConnector extends Connector
{
    use AcceptsJson;

    public function __construct(
        public RiotApiPlatform $platform = RiotApiPlatform::EUW1
    ){
    }

    /**
     * Default headers for every request
     *
     * @return string[]
     */
    protected function defaultHeaders(): array
    {
        return [
            'X-Riot-Token' => config('services.riot.api_key'),
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Charset' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Origin' => 'https://developer.riotgames.com',
        ];
    }

//    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
//    {
//        dd($response->status(), $response->json());
//    }


    /**
     * Default HTTP client options
     *
     * @return string[]
     */
    protected function defaultConfig(): array
    {
        return [
            'verify' => false,
        ];
    }

    public function resolveBaseUrl(): string
    {
        return '';
    }


}
