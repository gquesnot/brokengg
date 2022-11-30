<?php

namespace App\Data\match_timeline;

use App\Data\DataJsonCast;
use Arr;
use Livewire\Wireable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ParticipantFrameStatsData extends DataJsonCast implements Wireable
{
    public function __construct(
        public int $ad = 0,
        public int $ah = 0,
        public int $ap = 0,
        public int $as = 0,
        public int $hp = 0,
        public int $mr = 0,
        public int $ms = 0,
        public int $cdr = 0,
        public int $mana = 0,
        public int $armor = 0,
        public int $hp_regen = 0,
        public int $tenacity = 0,
        public int $mana_regen = 0,
        public int $magi_pen_flat = 0,
        public int $physical_vamp = 0,
        public int $armor_pen_flat = 0,
        public int $omnivamp_percent = 0,
        public int $armor_pen_percent = 0,
        public int $lifesteal_percent = 0,
        public int $magic_pen_percent = 0,
        public int $spell_vamp_percent = 0,
        public int $armor_pen_percent_bonus = 0,
        public int $magic_pen_percent_bonus = 0,
    ) {
    }

    public static function from_api($datas)
    {
        $stats = new self();
        $stats->ah = Arr::get($datas, 'abilityHaste', 0);
        $stats->ap = Arr::get($datas, 'abilityPower', 0);
        $stats->armor = Arr::get($datas, 'armor', 0);
        $stats->armor_pen_flat = Arr::get($datas, 'armorPen', 0);
        $stats->armor_pen_percent = Arr::get($datas, 'armorPenPercent', 0);
        $stats->ad = Arr::get($datas, 'attackDamage', 0);
        $stats->as = Arr::get($datas, 'attackSpeed', 0);
        $stats->armor_pen_percent_bonus = Arr::get($datas, 'bonusArmorPenPercent', 0);
        $stats->magic_pen_percent_bonus = Arr::get($datas, 'bonusMagicPenPercent', 0);
        $stats->tenacity = Arr::get($datas, 'ccReduction', 0);
        $stats->cdr = Arr::get($datas, 'cooldownReduction', 0);
        $stats->hp = Arr::get($datas, 'healthMax', 0);
        $stats->hp_regen = Arr::get($datas, 'healthRegen', 0);
        $stats->lifesteal_percent = Arr::get($datas, 'lifesteal', 0);
        $stats->magi_pen_flat = Arr::get($datas, 'magicPen', 0);
        $stats->magic_pen_percent = Arr::get($datas, 'magicPenPercent', 0);
        $stats->mr = Arr::get($datas, 'magicResist', 0);
        $stats->ms = Arr::get($datas, 'movementSpeed', 0);
        $stats->omnivamp_percent = Arr::get($datas, 'omnivamp', 0);
        $stats->physical_vamp = Arr::get($datas, 'physicalVamp', 0);
        $stats->mana = Arr::get($datas, 'powerMax', 0);
        $stats->mana_regen = Arr::get($datas, 'powerRegen', 0);
        $stats->spell_vamp_percent = Arr::get($datas, 'spellVamp', 0);

        return $stats;
    }
}
