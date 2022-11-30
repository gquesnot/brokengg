interface ChampionStatsInterface {
    hp: number;
    hp_per_level: number;
    hp_regen: number;
    hp_regen_per_level: number;
    mp: number;
    mp_per_level: number;
    mp_regen: number;
    mp_regen_per_level: number;
    ms: number;
    armor: number;
    armor_per_level: number;
    mr: number;
    mr_per_level: number;
    attack_range: number;
    ad: number;
    ad_per_level: number;
    crit_percent: number;
    crit_percent_per_level: number;
    base_as: number;
    as_percent_per_level: number;
}


export class ChampionStats implements ChampionStatsInterface {
    ad: number = 0;
    ad_per_level: number = 0;
    armor: number = 0;
    armor_per_level: number = 0;
    as_percent_per_level: number = 0;
    attack_range: number = 0;
    base_as: number = 0;
    crit_percent: number = 0;
    crit_percent_per_level: number = 0;
    hp: number = 0;
    hp_per_level: number = 0;
    hp_regen: number = 0;
    hp_regen_per_level: number = 0;
    mp: number = 0;
    mp_per_level: number = 0;
    mp_regen: number = 0;
    mp_regen_per_level: number = 0;
    mr: number = 0;
    mr_per_level: number = 0;
    ms: number = 0;

}
