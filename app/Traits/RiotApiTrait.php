<?php

namespace App\Traits;

use App\Exceptions\LolApiException;
use App\Models\Champion;
use App\Models\ItemSummonerMatch;
use App\Models\Map;
use App\Models\Matche;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Summoner;
use App\Models\Summoner as SummonerModel;
use App\Models\SummonerMatch;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait RiotApiTrait
{
    protected $client;

    protected $apiKey;

    public ?Collection $modes = null;
    public ?Collection $maps = null;
    public ?Collection $queues = null;

    public function setModels(){
        if ($this->modes == null ){
            $this->modes = Mode::select(['id', 'name'])->get();
            $this->maps = Map::pluck('id');
            $this->queues = Queue::pluck('id');
        }
    }

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
        $matchIds = collect($this->getAllMatchIds($summoner->puuid, null, null, $summoner->last_scanned_match))->reverse();
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

    public function clearMatches(){
        $ids = collect(DB::select('select t.id
                from (
                    select id,
                        count(*) as cnt
                    from summoner_matches
                    group by summoner_id, match_id
                ) as t
                where cnt > 1'))->map(function ($item) {
            //return $item->id as string;
            return (string) $item->id;
        });

        if ($ids->isNotEmpty()) {
            DB::delete('delete from summoner_matches where id in ('.implode(', ', $ids->toArray()).')');
        }
        Log::info('clear matches: '.$ids->count());
    }

    public function getAllMatchIds($puuid, $queuId = null, $startDate = null, $lastScannedMatch = null)
    {
        $res = new Collection();
        $offset = 0;
        $limit = 100;
        if ($startDate == null) {
            $startDate = Carbon::createFromFormat('d/m/Y', '16/02/2022')->timestamp;
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
            if ($tmp == null || count($tmp) < $limit) {
                break;
            }
            $offset += $limit;
        }

        return $res;
    }


    /**
     * @throws LolApiException
     */
    public function updateMatches(): void
    {
        $matches = Matche::whereUpdated( false)->get();
        $matchDone = 0;
        foreach ($matches as $match) {
            try{
                $check = $this->updateMatch($match);
            }catch (LolApiException $e){
                Log::info($matchDone.'/'.$matches->count().' Matches updated');
                Log::error($e->getMessage());
                throw $e;
            }

            if ($check === false) {
                Matche::where('id', $match->id)->delete();
            }
            $matchDone++;
        }
        Log::info($matchDone.'/'.$matches->count().' Matches updated');
        $this->clearMatches();
    }


    /**
     * @throws LolApiException
     */
    public function updateMatch($match): bool
    {
        $this->setModels();
        $data = $this->getMatchDetail($match->match_id);
        if ($data == null) {
             throw new LolApiException('Api Limit reached');
        }
        $info = $data->info;
        $mode = $this->modes->where('name', '=',$info->gameMode)->first();
        $hasMap = $this->maps->contains($info->mapId);
        $hasQueue = $this->queues->contains($info->queueId);
        if ($mode != null && $hasMap && $hasQueue) {
            SummonerMatch::where('match_id', $match->id)->delete();
            foreach ($info->participants as $participant) {
                $summoner = $this->getOrUpdateSummoner($participant);
                $championId = Champion::where('id', $participant->championId)->pluck('id')->first();
                if ($championId == null){
                    break;
                }
                $stats = [
                    'physical_damage_dealt' => $participant->physicalDamageDealt,
                    'physical_damage_dealt_to_champions' => $participant->physicalDamageDealtToChampions,
                    'physical_damage_taken' => $participant->physicalDamageTaken,
                    'magic_damage_dealt' => $participant->magicDamageDealt,
                    'magic_damage_dealt_to_champions' => $participant->magicDamageDealtToChampions,
                    'magic_damage_taken' => $participant->magicDamageTaken,
                    'true_damage_dealt' => $participant->trueDamageDealt,
                    'true_damage_dealt_to_champions' => $participant->trueDamageDealtToChampions,
                    'true_damage_taken' => $participant->trueDamageTaken,
                    'total_damage_dealt' => $participant->totalDamageDealt,
                    'total_damage_dealt_to_champions' => $participant->totalDamageDealtToChampions,
                    'total_damage_taken' => $participant->totalDamageTaken,
                    'total_heal' => $participant->totalHeal,
                    'total_time_cc_dealt' => $participant->totalTimeCCDealt,
                    'total_time_spent_dead' => $participant->totalTimeSpentDead,
                    'gold_earned' => $participant->goldEarned,
                    'gold_spent' => $participant->goldSpent,
                ];
                $summonerMatchParams = [
                    'summoner_id' => $summoner->id,
                    'match_id' => $match->id,
                    'won' => $participant->win,
                    'kills' => $participant->kills,
                    'deaths' => $participant->deaths,
                    'assists' => $participant->assists,
                    'champion_id' => $championId,
                    'champ_level' => $participant->champLevel,
                    'stats' => $stats,
                    'minions_killed' => $participant->totalMinionsKilled,
                    'largest_killing_spree' => $participant->largestKillingSpree,
                    'double_kills' => $participant->doubleKills,
                    'triple_kills' => $participant->tripleKills,
                    'quadra_kills' => $participant->quadraKills,
                    'penta_kills' => $participant->pentaKills,
                ];

                if (isset($participant->challenges)) {
                    $summonerMatchParams['challenges'] = $participant->challenges;
                }
                $kda = $participant->kills + $participant->assists;
                if ($participant->deaths > 0) {
                    $kda = $kda / $participant->deaths;
                }
                $summonerMatchParams['kda'] = $kda;
                $allKills = $info->teams[($participant->teamId == 100 ? 0 : 1)]->objectives->champion->kills;
                $summonerMatchParams['kill_participation'] = $participant->kills + $participant->assists;
                if ($allKills > 0) {
                    $summonerMatchParams['kill_participation'] = round($summonerMatchParams['kill_participation'] / $allKills, 2);
                }
                $sm = SummonerMatch::create($summonerMatchParams);
                $items = [];
                for ($i = 0; $i < 6; $i++) {
                    $item = $participant->{'item'.$i};
                    $items[]= [
                        'summoner_match_id' => $sm->id,
                        'item_id' => $item,
                        'position' => $i,
                    ];
                }
                ItemSummonerMatch::insert($items);
            }
            $match->mode_id = $mode->id;
            $match->map_id = $info->mapId;
            $match->queue_id = $info->queueId;
            $match->match_creation = Carbon::createFromTimestampMs($info->gameStartTimestamp)->format('Y-m-d H:i:s');
            $match->match_duration = Carbon::createFromTimestamp($info->gameDuration)->format('H:i:s');
            $match->updated = true;
            $match->save();
            return true;
        }

        return false;
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

    public function getOrUpdateSummoner($participant): Summoner{

        $summoner = SummonerModel::where('puuid', $participant->puuid)->select('id')->first();
        if ($summoner == null) {

            $summoner = SummonerModel::firstOrCreate([
                'puuid' => $participant->puuid,

            ], [
                'name' => $participant->summonerName,
                'profile_icon_id' => $participant->profileIcon,
                'summoner_level' => $participant->summonerLevel,
                'summoner_id' => $participant->summonerId,
            ]);
        }
        return $summoner;
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
