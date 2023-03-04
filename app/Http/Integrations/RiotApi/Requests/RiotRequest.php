<?php

namespace App\Http\Integrations\RiotApi\Requests;

use App\Enums\RiotApiPlatform;
use App\Http\Integrations\RiotApi\RiotPlatformApiConnector;
use App\Traits\HasDto;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\Statuses\ForbiddenException;
use Saloon\Http\Request;
use Saloon\Traits\Request\HasConnector;

class RiotRequest extends Request
{
    use HasConnector;


    /**
     * Define the HTTP method
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    protected string $connector = RiotPlatformApiConnector::class;

    public function __construct(
        RiotApiPlatform $platform = RiotApiPlatform::EUW1,
    )
    {
        $this->setConnector(new $this->connector($platform));
    }

    public function sendAndRetry(): \Saloon\Contracts\Response
    {
        return $this->connector()->sendAndRetry($this, 3, 10000, function ($exception, $pendingRequest) {
            if ($exception instanceof ForbiddenException) {
                return false;
            }
            return true;
        });
    }

    /**
     * Define the endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return '';
    }
}
