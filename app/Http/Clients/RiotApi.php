<?php

namespace App\Http\Clients;

use App\Data\match_timeline\ParticipantData;
use App\Exceptions\RiotApiForbiddenException;
use App\Models\Matche;
use App\Models\Summoner;
use App\Models\SummonerMatch;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RiotApi
{
    public string $api_key;

    public function __construct()
    {
        $this->api_key = config('services.riot.api_key');
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function getAllMatchIds(Summoner $summoner, $queueId = null, $startDate = null): Collection
    {
        $res = new Collection();
        $offset = 0;
        $limit = 100;
        $max_match_count = config('lol.max_match_count');
        if ($startDate == null) {
            $startDate = Carbon::createFromFormat('d/m/Y', config('lol.min_match_date'))->timestamp;
        }
        $last_scanned_match = Str::of($summoner->last_scanned_match)->replaceFirst('EUW1_', '')->toInteger();
        while (true) {
            $data = $this->getMatchIds($summoner, $queueId, $startDate, $limit, $offset);
            if ($data == null) {
                break;
            }
            $res = $res->merge($data);
            if (count($data) < $limit) {
                break;
            }
            $offset += $limit;
        }

        if ($max_match_count && $res->count() > $max_match_count) {
            $res = $res->slice(0, $max_match_count);
        }

        if ($last_scanned_match) {
            $res = $res->filter(fn ($id) => Str::of($id)->replaceFirst('EUW1_', '')->toInteger() > $last_scanned_match);
        }
        $summoner->last_scanned_match = $res->first();
        $summoner->save();

        return $res;
    }

    public function getCachedMatchTimeline(Matche $match)
    {
        return Cache::remember("match_timeline_{$match->match_id}", 60 * 60 * 24, function () use ($match) {
            return $this->getMatchTimeline($match);
        });
    }

    // SUMMONERS

    /**
     * @throws RiotApiForbiddenException
     */
    public function getSummonerByName(string $summonerName)
    {
        $summonerName = urlencode($summonerName);

        return $this->get("https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/{$summonerName}");
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function getSummonerByPuuid(string $puuid)
    {
        return $this->get("https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/$puuid");
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function getSummonerById(string $summonerId)
    {
        return $this->get("https://euw1.api.riotgames.com/lol/summoner/v4/summoners/$summonerId");
    }

    //LEAGUES

    /**
     * @throws RiotApiForbiddenException
     */
    public function getSummonerLeaguesById($encryptedSummonerId)
    {
        return $this->get("https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/{$encryptedSummonerId}");
    }

    //MATCHES

    /**
     * @throws RiotApiForbiddenException
     */
    public function getMatchDetail($matchId)
    {
        return $this->get("https://europe.api.riotgames.com/lol/match/v5/matches/$matchId");
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function getMatchTimeline(Matche $match): Collection
    {
        $url = "https://europe.api.riotgames.com/lol/match/v5/matches/{$match->match_id}/timeline";
        $match_timeline = $this->get($url)['info'];
        $match->load('participants:id,champion_id,summoner_id,won,match_id,perks', 'participants.champion:id,name,champion_id,stats,img_url', 'participants.items:id', 'participants.summoner:id,profile_icon_id,name,puuid');

        return $match->participants->map(function (SummonerMatch $participant, int $index) use ($match_timeline) {
            return ParticipantData::fromApi($participant, $index + 1, $match_timeline);
        });
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function getMatchIds(Summoner $summoner, $queueId = null, $startDate = 1641592824, $limit = 100, $offset = 0)
    {
        $url = "https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/{$summoner->puuid}/ids";
        $params = [
            'startTime' => $startDate,
            'count' => $limit,
            'start' => $offset,
        ];

        if ($queueId) {
            $params['queue'] = $queueId;
        }

        return $this->get($url, $params);
    }

    // LIVE GAMES

    /**
     * @throws RiotApiForbiddenException
     */
    public function getSummonerLiveGame(Summoner $summoner)
    {
        return $this->get("https://euw1.api.riotgames.com/lol/spectator/v4/active-games/by-summoner/$summoner->summoner_id");
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function get($url, $params = [])
    {
        $response = Http::withoutVerifying()->withHeaders($this->getHeaders())->get($url, $params);
        switch ($response->status()) {
            case 404:
                return null;
            case 403:
                throw new RiotApiForbiddenException("Riot API returned {$response->status()} for $url");
            case 429:
                sleep(30);

                return $this->get($url, $params);
            default:
                return $response->json();
        }
    }

    public function getHeaders(): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Charset' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Origin' => 'https://developer.riotgames.com',
            'X-Riot-Token' => $this->api_key,
        ];
    }
}
