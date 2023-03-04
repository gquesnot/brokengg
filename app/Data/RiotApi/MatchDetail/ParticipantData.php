<?php

namespace App\Data\RiotApi\MatchDetail;

use App\Data\RiotApi\MatchDetail\Participant\PerksData;
use App\Models\Item;
use Illuminate\Support\Arr;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class ParticipantData extends \Spatie\LaravelData\Data
{
    public function __construct(
        public int    $all_in_pings,
        public int    $assist_me_pings,
        public int    $assists,
        public int    $bait_pings,
        public int    $baron_kills,
        public int    $basic_pings,
        public int    $bounty_level,
        public int    $champ_experience,
        public int    $champ_level,
        public int    $champion_id,
        public string $champion_name,
        public int    $champion_transform,
        public int    $command_pings,
        public int    $consumables_purchased,
        public int    $damage_dealt_to_buildings,
        public int    $damage_dealt_to_objectives,
        public int    $damage_dealt_to_turrets,
        public int    $damage_self_mitigated,
        public int    $danger_pings,
        public int    $deaths,
        public int    $detector_wards_placed,
        public int    $double_kills,
        public int    $dragon_kills,
        public bool   $eligible_for_progression,
        public int    $enemy_missing_pings,
        public int    $enemy_vision_pings,
        public bool   $first_blood_assist,
        public bool   $first_blood_kill,
        public bool   $first_tower_assist,
        public bool   $first_tower_kill,
        public bool   $game_ended_in_early_surrender,
        public bool   $game_ended_in_surrender,
        public int $get_back_pings,
        public int    $gold_earned,
        public int    $gold_spent,
        public int    $hold_pings,
        public string $individual_position,
        public int    $inhibitor_kills,
        public int    $inhibitor_takedowns,
        public int    $inhibitors_lost,
        public int    $item_0,
        public int    $item_1,
        public int    $item_2,
        public int    $item_3,
        public int    $item_4,
        public int    $item_5,
        public int    $item_6,
        public int    $items_purchased,
        public int    $killing_sprees,
        public int    $kills,
        public string $lane,
        public int    $largest_critical_strike,
        public int    $largest_killing_spree,
        public int    $largest_multi_kill,
        public int    $longest_time_spent_living,
        public int    $magic_damage_dealt,
        public int    $magic_damage_dealt_to_champions,
        public int    $magic_damage_taken,
        public int       $need_vision_pings,
        public int       $neutral_minions_killed,
        public int       $nexus_kills,
        public int       $nexus_lost,
        public int       $nexus_takedowns,
        public int       $objectives_stolen,
        public int       $objectives_stolen_assists,
        public int       $on_my_way_pings,
        public int       $participant_id,
        public string    $penta_kills,
        public PerksData $perks,
        public int       $physical_damage_dealt,
        public int       $physical_damage_dealt_to_champions,
        public int       $physical_damage_taken,
        public int       $profile_icon,
        public int       $push_pings,
        public string    $puuid,
        public int       $quadra_kills,
        public string    $riot_id_name,
        public string    $riot_id_tagline,
        public string    $role,
        public int    $sight_wards_bought_in_game,
        public int    $spell_1_casts,
        public int    $spell_2_casts,
        public int    $spell_3_casts,
        public int    $spell_4_casts,
        public int    $summoner_1_casts,
        public int    $summoner_1_id,
        public int    $summoner_2_casts,
        public int    $summoner_2_id,
        public string $summoner_id,
        public string $summoner_level,
        public string $summoner_name,
        public bool $team_early_surrendered,
        public int    $team_id,
        public string $team_position,

        public int    $time_played,
        public int    $total_damage_dealt,
        public int    $total_damage_dealt_to_champions,
        public int    $total_damage_shielded_on_teammates,
        public int    $total_damage_taken,
        public int    $total_heal,
        public int    $total_heals_on_teammates,
        public int    $total_minions_killed,
        public int    $total_time_spent_dead,
        public int    $total_units_healed,
        public int    $triple_kills,
        public int    $true_damage_dealt,
        public int    $true_damage_dealt_to_champions,
        public int    $true_damage_taken,
        public int    $turret_kills,
        public int    $turret_takedowns,
        public int    $turrets_lost,
        public int    $unreal_kills,
        public int    $vision_cleared_pings,
        public int    $vision_score,
        public int    $vision_wards_bought_in_game,
        public int    $wards_killed,
        public int    $wards_placed,
        public bool   $win,
        public int    $time_ccing_others = 0,
        public int    $total_time_ccdealt= 0,
    )

    {
    }

    public function getItems(): array{
        $items = [];
        for ($i = 0; $i < 6; $i++) {
            $item_id = $this->{"item_$i"};
            if ($item_id == 0 || Item::whereId($item_id)->doesntExist()) {
                continue;
            }
            $items[] = [
                'item_id' => $item_id,
                'position' => $i,
            ];
        }
        return $items;
    }


    public function getKda()
    {
        if ($this->deaths == 0){
            return $this->kills + $this->assists;
        }
        return $this->kills + $this->assists / $this->deaths;
    }

    public function getKillParticipation(int $total_kills)
    {
        if ($total_kills == 0){
            return 0;
        }
        return $this->kills / $total_kills;
    }
}
