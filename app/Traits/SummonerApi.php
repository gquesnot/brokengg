<?php

namespace App\Traits;

use App\Data\match_timeline\PerksData;
use App\Enums\Rank;
use App\Enums\RankedType;
use App\Enums\Tier;
use App\Exceptions\RiotApiForbiddenException;
use App\Http\Clients\RiotApi;
use App\Models\Champion;
use App\Models\Item;
use App\Models\ItemSummonerMatch;
use App\Models\Map;
use App\Models\Matche;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Summoner;
use App\Models\SummonerLeague;
use App\Models\SummonerMatch;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait SummonerApi
{
    public function updateMatches(bool $force = false): void
    {
        Log::info('Updating matches for '.$this->name);
        try {
            $this->selfUpdate($force);
            $this->updateMatchesIds();
            $this->updateMatchesData();
        } catch (RiotApiForbiddenException $e) {
            Log::error('RiotApiForbiddenException: '.$e->getMessage());
        }
    }

    /**
     * @throws RiotApiForbiddenException
     */
    private function updateMatchesData(): void
    {
        $matches = Matche::whereUpdated(false)->whereIsTrashed(false)->get();

        foreach ($matches as $match) {
            if (! $this->updateMatch($match)) {
                $match->update(['is_trashed' => true]);
            }
        }
    }

    /**
     * @throws RiotApiForbiddenException
     */
    private function updateMatch(Matche $match): bool
    {
        $api = new RiotApi();
        $match_data = $api->getMatchDetail($match->match_id);
        $match_info = $match_data['info'];
        if ($match_info['gameMode'] == '' || $match_info['mapId'] == 0 || $match_info['queueId'] == '') {
            return false;
        }
        $mode = Mode::where('name', $match_info['gameMode'])->first();
        if ($mode == null || $match_info['queueId'] == 0) {
            return false;
        }
        // clear SummonerMatch and items related to this match
        ItemSummonerMatch::whereIn('summoner_match_id', $match->participants()->pluck('id'))->delete();
        $match->participants()->delete();
        foreach ($match_info['participants'] as $participant) {
            $summoner = Summoner::updateOrCreateWithParticipantData($participant);
            $champion_id = Champion::where('id', $participant['championId'])->pluck('id')->first();
            if ($champion_id == null) {
                break;
            }
            $stats = [
                'physical_damage_dealt' => $participant['physicalDamageDealt'],
                'physical_damage_dealt_to_champions' => $participant['physicalDamageDealtToChampions'],
                'physical_damage_taken' => $participant['physicalDamageTaken'],
                'magic_damage_dealt' => $participant['magicDamageDealt'],
                'magic_damage_dealt_to_champions' => $participant['magicDamageDealtToChampions'],
                'magic_damage_taken' => $participant['magicDamageTaken'],
                'true_damage_dealt' => $participant['trueDamageDealt'],
                'true_damage_dealt_to_champions' => $participant['trueDamageDealtToChampions'],
                'true_damage_taken' => $participant['trueDamageTaken'],
                'total_damage_dealt' => $participant['totalDamageDealt'],
                'total_damage_dealt_to_champions' => $participant['totalDamageDealtToChampions'],
                'total_damage_taken' => $participant['totalDamageTaken'],
                'total_heal' => $participant['totalHeal'],
                'total_time_cc_dealt' => $participant['totalTimeCCDealt'],
                'total_time_spent_dead' => $participant['totalTimeSpentDead'],
                'gold_earned' => $participant['goldEarned'],
                'gold_spent' => $participant['goldSpent'],
            ];
            $summoner_match_attributes = [
                'summoner_id' => $summoner->id,
                'match_id' => $match->id,
                'won' => $participant['win'],
                'kills' => $participant['kills'],
                'deaths' => $participant['deaths'],
                'assists' => $participant['assists'],
                'champion_id' => $champion_id,
                'champ_level' => $participant['champLevel'],
                'stats' => $stats,
                'minions_killed' => $participant['totalMinionsKilled'],
                'largest_killing_spree' => $participant['largestKillingSpree'],
                'double_kills' => $participant['doubleKills'],
                'triple_kills' => $participant['tripleKills'],
                'quadra_kills' => $participant['quadraKills'],
                'penta_kills' => $participant['pentaKills'],
            ];

            if (isset($participant['challenges'])) {
                $summoner_match_attributes['challenges'] = $participant['challenges'];
            }
            $kda = $participant['kills'] + $participant['assists'];
            if ($participant['deaths'] > 0) {
                $kda = $kda / $participant['deaths'];
            }
            $summoner_match_attributes['perks'] = PerksData::from([
                'offense' => $participant['perks']['statPerks']['offense'],
                'defense' => $participant['perks']['statPerks']['defense'],
                'flex' => $participant['perks']['statPerks']['flex'],
            ]);
            $summoner_match_attributes['kda'] = $kda;
            $all_game_kills = $match_info['teams'][($participant['teamId'] == 100 ? 0 : 1)]['objectives']['champion']['kills'];
            $summoner_match_attributes['kill_participation'] = $participant['kills'] + $participant['assists'];
            if ($all_game_kills > 0) {
                $summoner_match_attributes['kill_participation'] = round($summoner_match_attributes['kill_participation'] / $all_game_kills, 2);
            }
            $sm = SummonerMatch::create($summoner_match_attributes);

            $items = [];
            for ($i = 0; $i < 6; $i++) {
                $item_id = Arr::get($participant, 'item'.$i, 0);
                if ($item_id == 0 || ! Item::whereId($item_id)->exists()) {
                    continue;
                }
                $items[] = [
                    'summoner_match_id' => $sm->id,
                    'item_id' => $item_id,
                    'position' => $i,
                ];
            }
            ItemSummonerMatch::insert($items);
        }

        $match->mode_id = $mode->id;
        $match->map_id = $match_info['mapId'];
        $match->queue_id = $match_info['queueId'];

        $game_start = Carbon::createFromTimestamp($match_info['gameCreation'] / 1000);
        $game_duration = Carbon::createFromTimestamp($match_info['gameDuration']);
        $game_end = $game_start->copy()
            ->addSeconds($game_duration->second)
            ->addMinutes($game_duration->minute)
            ->addHours($game_duration->hour);
        $match->match_creation = $game_start->format('Y-m-d H:i:s');
        $match->match_duration = $game_duration->format('H:i:s');
        $match->match_end = $game_end->format('Y-m-d H:i:s');
        $match->updated = true;
        $match->save();

        return true;
    }

    /**
     * @throws RiotApiForbiddenException
     */
    private function updateMatchesIds(): void
    {
        $riotApi = new RiotApi();
        $matches_ids = $riotApi->getAllMatchIds($this);
        $founds = Matche::whereIn('match_id', $matches_ids)->pluck('match_id');
        $matches_ids = $matches_ids->diff($founds);
        if ($matches_ids->isNotEmpty()) {
            Matche::insert($matches_ids->map(function ($match_id) {
                return [
                    'match_id' => $match_id,
                ];
            })->toArray());
        }
    }

    public static function updateOrCreateWithParticipantData(array $participantData): Summoner
    {
        $summoner = Summoner::wherePuuid($participantData['puuid'])->first();
        if ($summoner && $summoner->summoner_level < $participantData['summonerLevel']) {
            $summoner->update([
                'name' => $participantData['summonerName'],
                'profile_icon_id' => $participantData['profileIcon'],
                'summoner_level' => $participantData['summonerLevel'],
            ]);
        }
        elseif (!$summoner){
            Summoner::create([
                'puuid' => $participantData['puuid'],
                'summoner_id' => $participantData['summonerId'],
                'name' => $participantData['summonerName'],
                'profile_icon_id' => $participantData['profileIcon'],
                'summoner_level' => $participantData['summonerLevel'],
            ]);
        }
        return $summoner;
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public static function updateOrCreateByName(string $summonerName, bool $force = false): ?Summoner
    {
        $api = new RiotApi();
        $summoner = Summoner::whereName($summonerName)->first();
        if ($force || ! $summoner || ! $summoner->complete || $summoner->updated_at->diffInDays(now()) > 7) {
            $summonerData = $api->getSummonerByName($summonerName);
            $summoner = Summoner::updateSummoner($summonerData);
        }

        return $summoner;
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public static function updateOrCreateByPuuid(string $puuid, bool $force = false): ?Summoner
    {
        $api = new RiotApi();
        $summoner = Summoner::wherePuuid($puuid)->first();
        if ($force || ! $summoner || ! $summoner->complete || $summoner->updated_at->diffInDays(now()) > 7) {
            $summonerData = $api->getSummonerByPuuid($puuid);
            $summoner = Summoner::updateSummoner($summonerData);
        }

        return $summoner;
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public static function updateOrCreateById(string $summonerId, bool $force = false): ?Summoner
    {
        $api = new RiotApi();
        $summoner = Summoner::whereSummonerId($summonerId)->first();
        if ($force || ! $summoner || ! $summoner->complete || $summoner->updated_at->diffInDays(now()) > 7) {
            $summonerData = $api->getSummonerById($summonerId);
            $summoner = Summoner::updateSummoner($summonerData);
        }

        return $summoner;
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function selfUpdate(bool $force = false): void
    {
        Summoner::updateOrCreateById($this->summoner_id, $force);
    }

    /**
     * @throws RiotApiForbiddenException
     */
    private static function updateSummoner(?array $summonerData): ?Summoner
    {
        if (! $summonerData) {
            return null;
        }
        $summoner = Summoner::updateOrCreate([
            'summoner_id' => $summonerData['id'],
        ], [
            'name' => $summonerData['name'],
            'profile_icon_id' => $summonerData['profileIconId'],
            'summoner_level' => $summonerData['summonerLevel'],
            'puuid' => $summonerData['puuid'],
            'account_id' => $summonerData['accountId'],
            'complete' => true,
        ]);
        $summoner->updateLeagues();

        return $summoner;
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function updateLeagues(): void
    {
        $api = new RiotApi();
        $leaguesData = $api->getSummonerLeaguesById($this->summoner_id);
        if (! empty($leaguesData)) {
            foreach ($leaguesData as $leagueData) {
                $type = $leagueData['queueType'] == 'RANKED_SOLO_5x5' ? RankedType::SOLO : RankedType::FLEX;
                SummonerLeague::updateOrCreate([
                    'summoner_id' => $this->id,
                    'type' => $type,
                ], [
                    'rank' => Rank::from($leagueData['rank']),
                    'tier' => Tier::from(Str::lower($leagueData['tier'])),
                ]);
            }
        }
    }

    /**
     * @throws RiotApiForbiddenException
     */
    public function getLiveGame(): array|null
    {
        $api = new RiotApi();
        $live_game_data = $api->getSummonerLiveGame($this);
        if (Arr::has($live_game_data, 'status')) {
            return null;
        }

        return [
            'info' => [
                'queue' => Queue::whereId($live_game_data['gameQueueConfigId'])->first(),
                'mode' => Mode::whereId($live_game_data['gameMode'])->first(),
                'map' => Map::whereId($live_game_data['mapId'])->first(),
                'duration' => Carbon::createFromTimestamp($live_game_data['gameStartTime'] / 1000)->diff(Carbon::now())->format('%H:%I:%S'),
            ],
            'participants' => $live_game_data['participants'],
        ];
    }
}
