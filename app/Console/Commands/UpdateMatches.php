<?php

namespace App\Console\Commands;

use App\Models\Champion;
use App\Models\ItemSummonerMatch;
use App\Models\Map;
use App\Models\Matche;
use App\Models\Mode;
use App\Models\Queue;
use App\Models\Summoner as SummonerModel;
use App\Models\SummonerMatch;
use App\Traits\RiotApiTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMatches extends Command
{
    use RiotApiTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update SummonerMatch and ItemSummonerMatch from not updated matches';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $matches = Matche::where('updated', false)->get();
        $matchDone = 0;
        foreach ($matches as $match) {
            $check = $this->updateMatch($match);
            if ($check === null) {
                break;
            } elseif ($check === false) {
                Matche::where('id', $match->id)->delete();
            }
            $matchDone++;
        }
        Log::info($matchDone.'/'.$matches->count().' Matches updated');
    }

    public function updateMatch($match)
    {
        $data = $this->getMatchDetail($match->match_id);
        if ($data == null) {
            return null;
        }
        $info = $data->info;
        $mode = Mode::where('name', $info->gameMode)->first();
        $map = Map::where('id', $info->mapId)->first();
        $queue = Queue::where('id', $info->queueId)->first();
        if ($mode != null && $map != null && $queue != null) {
            SummonerMatch::where('match_id', $match->id)->delete();
            foreach ($info->participants as $participant) {
                $summoner = SummonerModel::where('puuid', $participant->puuid)->first();
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
                $champion = Champion::where('id', $participant->championId)->first();
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
                    'champion_id' => $champion->id,
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

                for ($i = 0; $i < 6; $i++) {
                    $item = $participant->{'item'.$i};
                    if ($item != 0) {
                        ItemSummonerMatch::create([
                            'summoner_match_id' => $sm->id,
                            'item_id' => $item,
                            'position' => $i,
                        ]);
                    }
                }
                $match->mode_id = $mode->id;
                $match->map_id = $map->id;
                $match->queue_id = $queue->id;
                $match->match_creation = Carbon::createFromTimestampMs($info->gameStartTimestamp)->format('Y-m-d H:i:s');
                $match->match_duration = Carbon::createFromTimestamp($info->gameDuration)->format('H:i:s');
                $match->updated = true;
                $match->save();
            }

            return true;
        }

        return false;
    }
}
