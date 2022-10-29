<?php

namespace App\Traits;

use App\Models\Matche;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait RiotApiTrait
{
    protected $client;

    protected $apiKey;

    public function getSummonerByName($summonerName)
    {
        return $this->doGetWithRetry("https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/$summonerName");
    }

    public function doGetWithRetry($url, $params = [])
    {
        if ($this->client == null) {
            $this->initClient();
        }
        try {
            $res = $this->client->request('GET', $url, [
                'headers' => $this->getHeaders(),
                'query' => $params,
            ])->getBody()->getContents();

            return json_decode($res);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function initClient()
    {
        $this->client = new Client();
        $this->apiKey = config('lol.API_KEY');
    }

    public function getHeaders()
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Charset' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Origin' => 'https://developer.riotgames.com',
            'X-Riot-Token' => $this->apiKey,
        ];
    }

    public function getSummonerByPuuid($puuid)
    {
        return $this->doGetWithRetry("https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/$puuid");
    }

    public function getSummonerLiveGameByAccountId($accountId)
    {
        return $this->doGetWithRetry("https://euw1.api.riotgames.com/lol/spectator/v4/active-games/by-summoner/$accountId");
    }

    public function updateSummonerMatches($summoner)
    {
        $matchIds = (new Collection($this->getAllMatchIds($summoner->puuid, null, null, $summoner->last_scanned_match)))->reverse();
        $matchDbIds = Matche::whereIn('match_id', $matchIds)->pluck('match_id');
        $resultMatchIds = $matchIds->diff($matchDbIds);
        if ($resultMatchIds->isNotEmpty()) {
            $summoner->last_scanned_match = $resultMatchIds->last();
            $summoner->save();
            if (! $resultMatchIds->isEmpty()) {
                $summoner->save();
                foreach ($resultMatchIds as $id) {
                    Matche::create(['match_id' => $id]);
                }
            }
        }

        return $resultMatchIds;
    }

    public function getAllMatchIds($puuid, $queuId = null, $startDate = null, $lastScannedMatch = null)
    {
        $res = new Collection();
        $offset = 0;
        $limit = 100;
        if ($startDate == null) {
            $startDate = Carbon::createFromFormat('d/m/Y', '20/06/2021')->timestamp;
            //$startDate = Carbon::createFromFormat('d/m/Y','20/10/2022')->timestamp;
        }
        $lastScannedMatch = intval(Str::replaceFirst('EUW1_', '', $lastScannedMatch));

        while (true) {
            $tmp = $this->getMatchIds($puuid, $queuId, $startDate, $limit, $offset);
            if ($lastScannedMatch != null) {
                $tmp = collect($tmp)->filter(function ($item) use ($lastScannedMatch) {
                    $item = intval(Str::replaceFirst('EUW1_', '', $item));

                    return $item > $lastScannedMatch;
                })->toArray();
            }
            $res = $res->merge($tmp);
            if (count($tmp) < $limit) {
                break;
            }
            $offset += $limit;
        }

        return $res;
    }

    public function getMatchIds($puuid, $queueId = null, $startDate = 1641592824, $limit = 100, $offset = 0)
    {
        $url = "https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/$puuid/ids";
        $params = [
            'startTime' => $startDate,
            'count' => $limit,
            'start' => $offset,
        ];
        if ($queueId) {
            $params['queue'] = $queueId;
        }

        return $this->doGetWithRetry($url, $params);
    }

    public function getMatchDetail($matchId)
    {
        return $this->doGetWithRetry("https://europe.api.riotgames.com/lol/match/v5/matches/$matchId");
    }

    public function getSummonerLeague($summonerId)
    {
        return $this->doGetWithRetry("https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/$summonerId");
    }
}
