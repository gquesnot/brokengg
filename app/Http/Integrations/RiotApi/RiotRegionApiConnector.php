<?php

namespace App\Http\Integrations\RiotApi;

use App\Enums\RiotApiPlatform;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class RiotRegionApiConnector extends RiotApiConnector
{
    use AcceptsJson;




    /**
     * The Base URL of the API
     *
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return "https://{$this->platform->getRegion()->value}.api.riotgames.com";
    }

}
