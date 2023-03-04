<?php

namespace App\Http\Integrations\RiotApi\Requests;

use App\Enums\RiotApiPlatform;
use App\Http\Integrations\RiotApi\RiotRegionApiConnector;
use Saloon\Enums\Method;
use Saloon\Traits\Request\HasConnector;

class MatchIdsRequest extends RiotRequest
{
    use HasConnector;

    /**
     * Define the HTTP method
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    protected string $connector = RiotRegionApiConnector::class;

    public function __construct(
        public string   $puuid,
        public int      $start = 0,
        public int      $count = 20,
        public ?int     $queue_id = null,
        public int      $start_time = 1647468793, # 16/03/2022
        public ?int     $end_time = null,
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
        return '/lol/match/v5/matches/by-puuid/' . $this->puuid . '/ids';
    }

    public function resolveQuery(): array
    {
        $query = [
            'start' => $this->start,
            'count' => $this->count,
        ];
        if ($this->queue_id) {
            $query['queue'] = $this->queue_id;
        }
        if ($this->start_time) {
            $query['startTime'] = $this->start_time;
        }
        if ($this->end_time) {
            $query['endTime'] = $this->end_time;
        }
        return $query;
    }



    public function collect(?string $last_match_id=null,int $start = 0, int $count = 100): array
    {
        $this->count = $count;
       $this->query()->merge($this->resolveQuery());

        $this->start = $start;
        $done = false;
        $matches = [];
        while (!$done) {
            $response = $this->sendAndRetry();
            $matches = array_merge($matches, $response->json());
            if ($last_match_id && in_array($last_match_id, $response->json())){
                $done = true;
            }
            if (count($response->json()) < $count
                || (config('services.riot.max_match_count') && count($matches) > config('services.riot.max_match_count'))) {
                $done = true;
            } else {
                $this->start += $this->count;
                $this->query()->merge($this->resolveQuery());
            }
        }
        return $matches;
    }


}
