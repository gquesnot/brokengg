<?php

namespace App\Http\Integrations\RiotApi;

use App\Enums\RiotApiPlatform;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Contracts\Response;

class RiotPlatformApiConnector extends RiotApiConnector
{
    use AcceptsJson;



    /**
     * The Base URL of the API
     *
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return "https://{$this->platform->value}.api.riotgames.com";
    }


}
