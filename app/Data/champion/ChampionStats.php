<?php

namespace App\Data\champion;

use App\interfaces\DataMappingInterface;
use App\Traits\DataMappingTrait;
use App\Traits\JsonCastTrait;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ChampionStats extends Data implements DataMappingInterface
{
    use DataMappingTrait, JsonCastTrait;

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

    public static function getMapping(): array
    {
        return [
            'hp' => 'hp',
            'hpperlevel' => 'hp_per_level',
            'hpregen' => 'hp_regen',
            'hpregenperlevel' => 'hp_regen_per_level',
            'mp' => 'mp',
            'mpperlevel' => 'mp_per_level',
            'mpregen' => 'mp_regen',
            'mpregenperlevel' => 'mp_regen_per_level',
            'movespeed' => 'ms',
            'armor' => 'armor',
            'armorperlevel' => 'armor_per_level',
            'spellblock' => 'mr',
            'spellblockperlevel' => 'mr_per_level',
            'attackrange' => 'attack_range',
            'attackdamage' => 'ad',
            'attackdamageperlevel' => 'ad_per_level',
            'crit' => 'crit_percent',
            'critperlevel' => 'crit_percent_per_level',
            'attackspeed' => 'base_as',
            'attackspeedperlevel' => 'as_percent_per_level',
        ];
    }
}
