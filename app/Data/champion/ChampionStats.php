<?php

namespace App\Data\champion;

use App\Data\DataJsonCast;
use Illuminate\Support\Arr;
use Livewire\Wireable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ChampionStats extends DataJsonCast implements Wireable
{
    public function __construct(
        public int $hp = 0,
        public int $hp_per_level = 0,
        public int $hp_regen = 0,
        public int $hp_regen_per_level = 0,
        public int $mp = 0,
        public int $mp_per_level = 0,
        public int $mp_regen = 0,
        public int $mp_regen_per_level = 0,
        public int $ms = 0,
        public int $armor = 0,
        public int $armor_per_level = 0,
        public int $mr = 0,
        public int $mr_per_level = 0,
        public int $attack_range = 0,
        public int $ad = 0,
        public int $ad_per_level = 0,
        public float $crit_percent = 0,
        public float $crit_percent_per_level = 0,
        public float $base_as = 0,
        public float $as_percent_per_level = 0,
    ) {
    }

    public static function from_api($datas): ChampionStats
    {
        $stats = new ChampionStats();
        $stats->hp = Arr::get($datas, 'hp', 0);
        $stats->hp_per_level = Arr::get($datas, 'hpperlevel', 0);
        $stats->hp_regen = Arr::get($datas, 'hpregen', 0);
        $stats->hp_regen_per_level = Arr::get($datas, 'hpregenperlevel', 0);
        $stats->mp = Arr::get($datas, 'mp', 0);
        $stats->mp_per_level = Arr::get($datas, 'mpperlevel', 0);
        $stats->mp_regen = Arr::get($datas, 'mpregen', 0);
        $stats->mp_regen_per_level = Arr::get($datas, 'mpregenperlevel', 0);
        $stats->ms = Arr::get($datas, 'movespeed', 0);

        $stats->armor = Arr::get($datas, 'armor', 0);
        $stats->armor_per_level = Arr::get($datas, 'armorperlevel', 0);
        $stats->mr = Arr::get($datas, 'spellblock', 0);
        $stats->mr_per_level = Arr::get($datas, 'spellblockperlevel', 0);
        $stats->attack_range = Arr::get($datas, 'attackrange', 0);
        $stats->ad = Arr::get($datas, 'attackdamage', 0);
        $stats->ad_per_level = Arr::get($datas, 'attackdamageperlevel', 0);
        $stats->crit_percent = Arr::get($datas, 'crit', 0);
        $stats->crit_percent_per_level = Arr::get($datas, 'critperlevel', 0);
        $stats->base_as = Arr::get($datas, 'attackspeed', 0);
        $stats->as_percent_per_level = Arr::get($datas, 'attackspeedperlevel', 0);

        return $stats;
    }
}
