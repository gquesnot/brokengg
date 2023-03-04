<?php

namespace App\Traits;

use App\Data\match_timeline\PerksData;
use App\Data\RiotApi\LiveGameData;
use App\Data\RiotApi\MatchDetail\ParticipantData;
use App\Data\RiotApi\MatchDetailData;
use App\Data\RiotApi\SummonerData;
use App\Data\RiotApi\SummonerLeagueData;
use App\Exceptions\RiotApiForbiddenException;
use App\Http\Clients\RiotApi;
use App\Http\Integrations\RiotApi\Requests\LiveGameRequest;
use App\Http\Integrations\RiotApi\Requests\MatchDetailRequest;
use App\Http\Integrations\RiotApi\Requests\MatchIdsRequest;
use App\Http\Integrations\RiotApi\Requests\SummonerByIdRequest;
use App\Http\Integrations\RiotApi\Requests\SummonerByNameRequest;
use App\Http\Integrations\RiotApi\Requests\SummonerByPuuidRequest;
use App\Http\Integrations\RiotApi\Requests\SummonerLeaguesRequest;
use App\Models\Champion;
use App\Models\Item;
use App\Models\ItemSummonerMatch;
use App\Models\Map;
use App\Models\Matche;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Summoner;
use App\Models\SummonerMatch;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\DataCollection;

trait SummonerApi
{
    public function updateMatches(bool $force = false): void
    {
        Log::info('Updating matches for ' . $this->name);
        try {
            $this->selfUpdate($force);
            $this->updateMatchesIds();
            $this->updateMatchesData();
        } catch (RiotApiForbiddenException $e) {
            Log::error('RiotApiForbiddenException: ' . $e->getMessage());
        }
    }

    /**
     * @throws RiotApiForbiddenException
     */
    private function updateMatchesData(): void
    {
        $matches = Matche::whereUpdated(false)->whereIsTrashed(false)->get();

        foreach ($matches as $match) {
            if (!$this->updateMatch($match)) {
                $match->update(['is_trashed' => true]);
            }
        }
    }


    private function updateMatch(Matche $match): bool
    {
        $match_data = self::getMatchDetailData($match->match_id);
        if (
            $match_data->info->game_mode == ''
            || $match_data->info->map_id == 0
            || $match_data->info->queue_id == ''
            || $match_data->info->queue_id == 0
            || Mode::where('name', $match_data->info->game_mode)->doesntExist()
        ) {
            return false;
        }

        // clear SummonerMatch and items related to this match
        ItemSummonerMatch::whereIn('summoner_match_id', $match->participants()->pluck('id'))->delete();
        $match->participants()->delete();
        $match_data->info->participants->each(function (ParticipantData $participant) use ($match, $match_data) {
            $summoner = Summoner::updateOrCreateWithParticipantData($participant);
            if (Champion::whereId($participant->champion_id)->doesntExist()) {
                return;
            }
            $total_kills = collect($match_data->info->participants->filter(function (ParticipantData $p) use ($participant) {
                return $p->team_id == $participant->team_id;
            })->map(function (ParticipantData $p) {
                return $p->kills;
            })->toArray())->sum();
            $summoner_match = $summoner->matches()->create([
                'match_id' => $match->id,
                'won' => $participant->win,
                'kills' => $participant->kills,
                'deaths' => $participant->deaths,
                'assists' => $participant->assists,
                'champion_id' => $participant->champion_id,
                'champ_level' => $participant->champ_level,
                'participant_data' => $participant->toArray(),
                'minions_killed' => $participant->total_minions_killed,
                'perks' => $participant->perks->stat_perks,
                'kda' => $participant->getKda(),
                'kill_participation' => $participant->getKillParticipation($total_kills),
            ]);
            $summoner_match->items()->createMany($participant->getItems());
        });

        $match->mode_id = Mode::where('name', $match_data->info->game_mode)->first()->id;
        $match->map_id = $match_data->info->map_id;
        $match->queue_id = $match_data->info->queue_id;

        $game_start = Carbon::createFromTimestamp($match_data->info->game_start_timestamp / 1000);
        $game_duration = Carbon::createFromTimestamp($match_data->info->game_duration);
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

    public function updateMatchesIds(): void
    {

        $matches_ids = self::getMatchIdsData($this->puuid, $this->last_scanned_match);

        $founds = Matche::whereIn('match_id', $matches_ids)->pluck('match_id');
        $matches_ids = $matches_ids->diff($founds);
        $this->last_scanned_match = $matches_ids->first();
        $this->save();
        if ($matches_ids->isNotEmpty()) {
            Matche::insert($matches_ids->map(function ($match_id) {
                return [
                    'match_id' => $match_id,
                ];
            })->toArray());
        }
    }

    public static function updateOrCreateWithParticipantData(ParticipantData $participant_data): Summoner
    {
        $summoner = Summoner::wherePuuid($participant_data->puuid)->first();
        if ($summoner) {
            if ($summoner->summoner_level < $participant_data->summoner_level) {
                $summoner->update([
                    'name' => $participant_data->summoner_name,
                    'profile_icon_id' => $participant_data->profile_icon,
                    'summoner_level' => $participant_data->summoner_level,
                ]);
            }
        } else {
            $summoner = Summoner::create([
                'puuid' => $participant_data->puuid,
                'name' => $participant_data->summoner_name,
                'profile_icon_id' => $participant_data->profile_icon,
                'summoner_level' => $participant_data->summoner_level,
            ]);
        }

        return $summoner;
    }

    public static function updateOrCreateByName(string $summonerName, bool $force = false): ?Summoner
    {
        $summoner = Summoner::whereName($summonerName)->first();

        if ($force || !$summoner || !$summoner->complete || $summoner->updated_at->diffInDays(now()) > 7) {
            $summoner_data = self::getSummonerByNameData($summonerName);
            if ($summoner_data) {
                $summoner = Summoner::updateSummoner($summoner_data);
            }
        }

        return $summoner;
    }


    public static function updateOrCreateByPuuid(string $puuid, bool $force = false): ?Summoner
    {
        $summoner = Summoner::wherePuuid($puuid)->first();
        if ($force || !$summoner || !$summoner->complete || $summoner->updated_at->diffInDays(now()) > 7) {
            $summoner_data = self::getSummonerByPuuidData($puuid);
            if ($summoner_data) {
                $summoner = Summoner::updateSummoner($summoner_data);
            }
        }
        return $summoner;
    }


    public static function updateOrCreateById(string $summonerId, bool $force = false): ?Summoner
    {
        $summoner = Summoner::whereSummonerId($summonerId)->first();
        if ($force || !$summoner || !$summoner->complete || $summoner->updated_at->diffInDays(now()) > 7) {
            $summoner_data = self::getSummonerByIdData($summonerId);
            if ($summoner_data) {
                $summoner = Summoner::updateSummoner($summoner_data);
            }
        }
        return $summoner;
    }


    public function selfUpdate(bool $force = false): void
    {
        Summoner::updateOrCreateById($this->summoner_id, $force);
    }


    private static function updateSummoner(\App\Data\RiotApi\SummonerData $summoner_data): ?Summoner
    {
        $summoner = Summoner::updateOrCreate([
            'summoner_id' => $summoner_data->id,
        ], Arr::except($summoner_data->toArray(), ['id']));
        $summoner->updateLeagues();

        return $summoner;
    }


    public function updateLeagues(): void
    {
        $leagues = self::getSummonerLeaguesData($this->summoner_id);
        $leagues->each(function (SummonerLeagueData $league) {
            $this->leagues()->updateOrCreate([
                'type' => $league->type,
            ], [
                'rank' => $league->rank,
                'tier' => $league->tier,
                'rank_number' => $league->tier->number() + $league->rank->number($league->tier),
            ]);
        });
    }


    public function getLiveGame(): array|null
    {
        try {
            $live_game_data = self::getLiveGameData($this->summoner_id);
            return [
                'info' => [
                    'queue' => Queue::whereId($live_game_data->game_queue_config_id)->first(),
                    'mode' => Mode::whereId($live_game_data->game_mode)->first(),
                    'map' => Map::whereId($live_game_data->map_id)->first(),
                    'duration' => Carbon::createFromTimestamp($live_game_data->game_start_time / 1000)->diff(Carbon::now())->format('%H:%I:%S'),
                ],
                'participants' => $live_game_data->participants,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }


    static function getMatchDetailData(string $match_id): ?MatchDetailData
    {
        $request = (new MatchDetailRequest(match_id: $match_id))->sendAndRetry();
        if ($request->failed()) {
            return null;
        }
        return $request->dto();
    }


    static function getSummonerByIdData(string $summoner_id): ?SummonerData{
        $request = (new SummonerByIdRequest(summoner_id: $summoner_id))->sendAndRetry();
        if ($request->failed()) {
            return null;
        }
        return $request->dto();
    }

    static function getSummonerByNameData(string $summoner_name): ?SummonerData{
        $request = (new SummonerByNameRequest(summoner_name: $summoner_name))->sendAndRetry();
        if ($request->failed()) {
            return null;
        }
        return $request->dto();
    }

    static function getSummonerByPuuidData(string $puuid): ?SummonerData{
        $request = (new SummonerByPuuidRequest(puuid: $puuid))->sendAndRetry();
        if ($request->failed()) {
            return null;
        }
        return $request->dto();
    }

    static function getSummonerLeaguesData(string $summoner_id): ?DataCollection{
        $request = (new SummonerLeaguesRequest(summoner_id: $summoner_id))->sendAndRetry();
        if ($request->failed()) {
            return null;
        }
        return $request->dto();
    }

    static function getLiveGameData(string $summoner_id): ?LiveGameData{
        $request = (new LiveGameRequest(summoner_id: $summoner_id))->sendAndRetry();
        if ($request->failed()) {
            return null;
        }
        return $request->dto();
    }

    static function getMatchIdsData(string $puuid, ?string $last_scanned_match): Collection{
        return collect((new MatchIdsRequest($puuid))->collect($last_scanned_match));
    }

}
