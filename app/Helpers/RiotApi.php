<?php

namespace App\Helpers;

use App\Exceptions\LolApiException;
use App\Models\ApiAccount;
use App\Models\Champion;
use App\Models\ItemSummonerMatch;
use App\Models\Map;
use App\Models\Matche;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Summoner;
use App\Models\SummonerApi;
use App\Models\SummonerMatch;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class  RiotApi
{
    protected $client;

    public ?Collection $modes = null;
    public ?Collection $maps = null;
    public ?Collection $queues = null;
    public Collection $apiAccounts;
    public ?int $current_account_id = 0;
    public ?Summoner $summoner;
    public Carbon $start;

    readonly public int $request_limit;
    readonly public int $request_limit_seconds;


    public function __construct(Summoner $summoner = null)
    {
        $this->request_limit = 100;
        $this->request_limit_seconds = 120;
        $this->summoner = $summoner;
        $this->apiAccounts = Cache::remember('apiAccounts', 120, function () {
            return ApiAccount::whereActif(true)->get();
        });
        $this->client = new Client();
        $this->modes = Mode::select(['id', 'name'])->get();
        $this->maps = Map::pluck('id');
        $this->queues = Queue::pluck('id');
        $this->reset();
    }


    # save cache when RiotApi is destructed
    public function __destruct()
    {
        Cache::put('apiAccounts', $this->apiAccounts, 120);
    }

    public function getSummonerNameByPuuid(string $puuid): ?string
    {
        $summoner = SummonerApi::wherePuuid($puuid)->whereAccountId($this->getAccount()->id)->with('summoner')->first()?->summoner;
        if ($summoner == null) {
            return $this->getSummonerByPuuid($puuid)?->name;
        }
        return $summoner->name;
    }


    public function createOrGetTmpSummoner($data, ApiAccount $account): Summoner
    {
        $summoner = SummonerApi::wherePuuid($data->puuid)->whereAccountId($account->id)->with('summoner')->first()?->summoner;
        if (!$summoner) {
            $summoner = Summoner::whereName($data->summonerName)->first();
        }
        if (!$summoner) {
            $summoner = Summoner::firstOrCreate([
                'name' => $data->summonerName,
                'summoner_level' => $data->summonerLevel,
                'profile_icon_id' => $data->profileIcon,
                'complete' => false,
            ]);
            SummonerApi::firstOrCreate([
                'summoner_id' => $summoner->id,
                'api_account_id' => null,
                'puuid' => $data->puuid,
                'account_id' => $account->id,
                'api_summoner_id' => $data->summonerId,
            ]);
        }
        return $summoner;
    }

    public function retryFn($callback, $accountId = null, $count = 0)
    {
        if ($count > count($this->apiAccounts) * 2) {
            dd('No more api accounts');
        }
        $result = $callback();
        if ($result == null) {
            if ($accountId == null) {
                $this->nextApi();
            } else {
                $this->waitApiOk($accountId);
            }
            $result = $this->retryFn($callback, $accountId, $count + 1);
        }
        return $result;
    }


    public function getAndUpdateSummonerByName(string $summonerName): Summoner
    {
        $summoner = Summoner::where('name', $summonerName)->first();
        if ($summoner == null || !$summoner->complete) {

            foreach ($this->apiAccounts as $account) {
                $hasAlready = SummonerApi::where('summoner_id', $summoner?->id)->where('account_id', $account->id)->first();
                if ($hasAlready) {
                    if ($hasAlready->api_account_id == null){

                    }
                    continue;
                }
                $summonerData = $this->retryFn(fn() => $this->getSummonerByName($summonerName, $account->api_key), $account->id);
                $summoner = $this->updateSummoner($summoner, $summonerData, $account);
            }
        }
        $summoner->complete = true;
        $summoner->save();
        return $summoner;
    }

    public function updateSummonerMatches()
    {
        $matchIds = collect($this->getAllMatchIds(null, null))->reverse();
        $matchDbIds = Matche::whereIn('match_id', $matchIds)->pluck('match_id');
        $resultMatchIds = $matchIds->diff($matchDbIds);
        if ($resultMatchIds->isNotEmpty()) {
            $this->summoner->last_scanned_match = $resultMatchIds->last();
            $this->summoner->save();
            Matche::insert($resultMatchIds->map(fn($id) => ['match_id' => $id])->toArray());
        }

        return $resultMatchIds;
    }

    public function getAllMatchIds($queuId = null, $startDate = null)
    {
        $res = new Collection();
        $offset = 0;
        $limit = 100;
        if ($startDate == null) {
            $startDate = Carbon::createFromFormat('d/m/Y', '16/02/2022')->timestamp;
        }
        $lastScannedMatch = intval(Str::replaceFirst('EUW1_', '', $this->summoner->last_scanned_match));
        while (true) {

            $data = $this->retryFn(fn() => $this->getMatchIds($queuId, $startDate, $limit, $offset));
            if ($lastScannedMatch != null) {
                $data = collect($data)->filter(function ($item) use ($lastScannedMatch) {
                    if ($item == null) {
                        return false;
                    }
                    $item = intval(Str::replaceFirst('EUW1_', '', $item));
                    return $item > $lastScannedMatch;
                })->toArray();
            }
            $res = $res->merge($data);
            if ($data == null || count($data) < $limit) {
                break;
            }
            $offset += $limit;
        }

        return $res;
    }


    public function updateMatches(): void
    {
        $matches = Matche::whereUpdated(false)->get();
        $matchDone = 0;
        foreach ($matches as $match) {
            try {
                $check = $this->updateMatch($match);
            } catch (LolApiException $e) {
                Log::info($matchDone . '/' . $matches->count() . ' Matches updated');
                Log::error($e->getMessage());
                #throw $e;
                return;
            }

            if ($check === false) {
                Matche::where('id', $match->id)->delete();
            }
            $matchDone++;
        }
        Log::info($matchDone . '/' . $matches->count() . ' Matches updated');
        $this->clearMatches();
    }


    /**
     * @throws LolApiException
     */
    public function updateMatch(Matche $match): bool
    {
        $data = $this->retryFn(fn() => $this->getMatchDetail($match->match_id));
        $info = $data->info;
        $mode = $this->modes->where('name', '=', $info->gameMode)->first();
        $hasMap = $this->maps->contains($info->mapId);
        $hasQueue = $this->queues->contains($info->queueId);
        if ($mode == null || !$hasMap || !$hasQueue) {
            return false;
        }
        SummonerMatch::where('match_id', $match->id)->delete();
        foreach ($info->participants as $participant) {
            $summoner = $this->createOrGetTmpSummoner($participant, $this->getAccount());
            $championId = Champion::where('id', $participant->championId)->pluck('id')->first();
            if ($championId == null) {
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
                $item = $participant->{'item' . $i};
                $items[] = [
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


    public function updateIncompleteSummoners()
    {
        $summoners = Summoner::whereComplete(false)->with('summonerApis.apiAccount')->get();
        foreach ($summoners as $summoner) {
            $summonerApi = $summoner->summonerApis->first();
            if ($summonerApi == null) {
                dd('no summoner api', $summoner);
            }
//            $summonerData = $this->retryFn(
//                fn() => $this->getSummonerByPuuid($summonerApi->puuid, $summonerApi->apiAccount->api_key),
//                $summonerApi->account_id
//            );
//            $summoner->update([
//                'name' => $summonerData->name,
//                'revision_date' => intval($summonerData->revisionDate),
//            ]);
//            $summonerApi->update([
//                'api_account_id' => $summonerData->accountId,
//            ]);

            foreach ($this->apiAccounts as $account) {
                if ($account['id'] != $summonerApi->apiAccount->id) {
                    $summonerDataName = $this->retryFn(fn () => $this->getSummonerByName($summoner->name, $account->api_key), $account->id);
                    $this->updateSummoner($summoner, $summonerDataName, $account);
                }
            }
            $summoner->complete = $summoner->summonerApis()->count() == count($this->apiAccounts);
            $summoner->save();
        }

    }

    public function clearMatches()
    {
        $ids = collect(DB::select('select t.id
                from (
                    select id,
                        count(*) as cnt
                    from summoner_matches
                    group by summoner_id, match_id
                ) as t
                where cnt > 1'))->map(function ($item) {
            //return $item->id as string;
            return (string)$item->id;
        });

        if ($ids->isNotEmpty()) {
            DB::delete('delete from summoner_matches where id in (' . implode(', ', $ids->toArray()) . ')');
        }
        Log::info('clear matches: ' . $ids->count());
    }

    public function nextApi()
    {
        $this->waitApiOk(accountId: null, save_current_account_id: true);
        return true;
    }

    public function hasApiOk($accountId = null): int
    {
        $this->updateLimitReached();
        if ($accountId != null) {
            $account = $this->getAccount($accountId);
            return !$account->limit_reached ? $account->id : -1;
        }

        foreach ($this->apiAccounts as $key => $account) {
            if (!$account->limit_reached) {
                return $account->id;
            }

        }
        return -1;
    }

    public function waitApiOk($accountId = null, $save_current_account_id = true)
    {
        $account = null;
        $account_id = $this->hasApiOk($accountId);
        if ($account_id == -1){
            if ($accountId != null){
                $account = $this->getAccount($accountId);
            }
            else{
                foreach ($this->apiAccounts as $key => $apiAccount) {
                    if ($account == null){
                        $account = $apiAccount;
                    }
                    else{
                        if ($apiAccount->restart_at->lt($account->restart_at)){
                            $account = $apiAccount;
                        }
                    }
                }
            }
        }
        else{
            $account = $this->getAccount($account_id);
        }
        if ($save_current_account_id){
            $this->current_account_id = $account->id;
        }
        if ($account_id == -1){
            $now = Carbon::now();
            $diffHuman= $account->restart_at->diffForHumans($now);
            //echo "waiting for api rate limit of {$account->username} : {$diffHuman}".PHP_EOL ;
            sleep($now->diffInSeconds($account->restart_at));
            //echo 'done waiting'.PHP_EOL;
        }
        return true;
    }

    public function getApiKey()
    {
        return $this->getAccount()->api_key;
    }


    public function getAccount($accountId = null): ?ApiAccount
    {
        if ($accountId == null) {
            $accountId = $this->current_account_id;
        }
        return $this->apiAccounts->filter(fn($item) => $item->id == $accountId)->first();
    }


    public function setRequestError($retry_after , $apiKey)
    {
        $account = $this->apiAccounts->filter(fn($item) => $item->api_key == $apiKey)->first();
        //echo 'limit reached on '. $account->username  . PHP_EOL;

        if (is_array($retry_after)){
            $retry_after = $retry_after[0];
        }
        if (is_string($retry_after)){
            $retry_after = intval($retry_after);
        }
        $retry_after += 5;
        $account->restart_at = Carbon::now()->addSeconds($retry_after);
        $account->limit_reached = true;
        $this->updateLimitReached();
    }


    public function updateLimitReached()
    {
        $now = Carbon::now();
        $this->apiAccounts->each(function (ApiAccount $account) use ($now) {
            if ($account->limit_reached){
                if ($account->restart_at->lte($now)){
                    $account->limit_reached = false;
                    $account->restart_at = null;
                }
            }
        });
    }

    public function doGetWithRetry($url, $params = [], $apiKey = null)
    {
        try {
            $all_params = [
                "headers" => $this->getHeaders($apiKey),
                "query" => $params,
            ];
            $response = $this->client->request('GET', $url, $all_params);
            $json = json_decode($response->getBody()->getContents());
            if ($json != null) {
                return $json;
            } else {
                echo 'error '. PHP_EOL;
                dd('error', );
            }
        } catch (GuzzleException $e) {
            if (str_contains($e->getMessage() ,"\"message\":\"Data not found\"") ){
                return null;
            }
            dd($e->getMessage());
            $retry_after = $e->getResponse()->getHeaders()['Retry-After'];
            $this->setRequestError($retry_after, $apiKey ?? $this->getApiKey());
        }
        return null;
    }

    public function getMatchIds($queueId = null, $startDate = 1641592824, $limit = 100, $offset = 0)
    {
        $puuid = $this->summoner->summonerApis->filter(fn($item) => $item->account_id = $this->current_account_id)->first()->puuid;
        $url = "https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/{$puuid}/ids";
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

    public function getSummonerByName($summonerName, $apiKey)
    {
        $summonerName = urlencode($summonerName);
        return $this->doGetWithRetry("https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/{$summonerName}", [], $apiKey);
    }

    public function getMatchDetail($matchId)
    {
        return $this->doGetWithRetry("https://europe.api.riotgames.com/lol/match/v5/matches/$matchId");
    }


    public function getSummonerByPuuid($puuid, $apiKey = null)
    {
        return $this->doGetWithRetry("https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/$puuid", [], $apiKey);
    }

    public function getSummonerLiveGameByAccountId(Summoner $summoner)
    {
        $summonerApi = $summoner->summonerApis->filter(fn($item) => $item->account_id == $this->current_account_id)->first();
        return $this->doGetWithRetry("https://euw1.api.riotgames.com/lol/spectator/v4/active-games/by-summoner/$summonerApi->api_summoner_id");
    }

    public function getSummonerLeague($summonerId)
    {
        return $this->doGetWithRetry("https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/$summonerId");
    }

    public function getHeaders($apiKey = null)
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Charset' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Origin' => 'https://developer.riotgames.com',
            'X-Riot-Token' => $apiKey ?? $this->getApiKey(),
        ];
    }


    /**
     * @param mixed $summoner
     * @param mixed $summonerData
     * @param mixed $account
     * @return Summoner|\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    private function updateSummoner(?Summoner $summoner, mixed $summonerData, ApiAccount $account): mixed
    {

        if (!$summoner) {
            $summoner = SummonerApi::wherePuuid($summonerData->puuid)->whereApiAccountId($account->id)->with('summoner')->first()?->summoner;
            if ($summoner) {
                $summoner->name = $summonerData->name;
                $summoner->profile_icon_id = intval($summonerData->profileIconId);
                $summoner->revision_date = intval($summonerData->revisionDate);
                $summoner->summoner_level = intval($summonerData->summonerLevel);
                $summoner->save();
            }
        }
        if (!$summoner) {
            $summoner = Summoner::firstOrCreate([
                'name' => $summonerData->name,
                'profile_icon_id' => intval($summonerData->profileIconId),
                'revision_date' => intval($summonerData->revisionDate),
                'summoner_level' => intval($summonerData->summonerLevel),
            ]);
        }

        SummonerApi::firstOrCreate([
            'summoner_id' => $summoner->id,
            'api_account_id' => $summonerData->accountId,
            'puuid' => $summonerData->puuid,
            'account_id' => $account->id,
            'api_summoner_id' => $summonerData->id,
        ]);
        return $summoner;
    }

    public function reset()
    {
        $this->start = Carbon::now();
        $this->current_account_id = $this->apiAccounts->first()->id;

    }
}
