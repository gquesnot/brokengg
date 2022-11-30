<?php

namespace App\Data\item;

use App\interfaces\DataMappingInterface;
use App\Traits\DataMappingTrait;
use App\Traits\JsonCastTrait;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ItemStats extends Data implements DataMappingInterface
{
    use DataMappingTrait, JsonCastTrait;

    public function __construct(
        public int $hp = 0,
        public int $ap = 0,
        public int $ad = 0,
        public int $armor = 0,
        public int $mr = 0,
        public int $ms = 0,
        public int $ms_percent = 0,
        public int $mp = 0,
        public int $ah = 0,
        public int $flat_magic_pen = 0,
        public int $flat_armor_pen = 0,
        public float $crit_percent = 0,
        public float $as_percent = 0,
        public float $hp_regen_percent = 0,
        public float $mp_regen_percent = 0,
        public float $life_steal_percent = 0,
        public float $omnivamp_percent = 0,
        public float $magic_pen_percent = 0,
        public float $armor_pen_percent = 0,
        public float $heal_power_percent = 0,
        public float $tenacity_percent = 0,
    ) {
    }

    public static function getMapping(): array
    {
        return [
            'move speed' => 'ms',
            'health' => 'hp',
            'critical strike chance percent' => 'crit_percent',
            'ability power' => 'ap',
            'mana' => 'mp',
            'armor' => 'armor',
            'magic resist' => 'mr',
            'attack damage' => 'ad',
            'attack speed percent' => 'as_percent',
            'life steal percent' => 'life_steal_percent',
            'ability haste' => 'ah',
            'base mana regen percent' => 'mp_regen_percent',
            'magic penetration' => 'flat_magic_pen',
            'move speed percent' => 'ms_percent',
            'armor penetration percent' => 'armor_pen_percent',
            'base health regen percent' => 'hp_regen_percent',
            'omnivamp percent' => 'omnivamp_percent',
            'heal and shield power percent' => 'heal_power_percent',
            'tenacity percent' => 'tenacity_percent',
            'magic penetration percent' => 'magic_pen_percent',
            'lethality' => 'flat_armor_pen',
        ];
    }
}
