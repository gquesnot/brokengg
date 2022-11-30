<?php

namespace App\Data\match_timeline;

use App\interfaces\DataMappingInterface;
use App\Traits\DataMappingTrait;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ParticipantFrameStatsData extends Data implements DataMappingInterface
{
    use DataMappingTrait;

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

    public static function getMapping(): array
    {
        return [
            'abilityHaste' => 'ah',
            'abilityPower' => 'ap',
            'armor' => 'armor',
            'armorPen' => 'armor_pen_flat',
            'armorPenPercent' => 'armor_pen_percent',
            'attackDamage' => 'ad',
            'attackSpeed' => 'as',
            'bonusArmorPenPercent' => 'armor_pen_percent_bonus',
            'bonusMagicPenPercent' => 'magic_pen_percent_bonus',
            'ccReduction' => 'tenacity',
            'cooldownReduction' => 'cdr',
            'healthMax' => 'hp',
            'healthRegen' => 'hp_regen',
            'lifesteal' => 'lifesteal_percent',
            'magicPen' => 'magi_pen_flat',
            'magicPenPercent' => 'magic_pen_percent',
            'magicResist' => 'mr',
            'movementSpeed' => 'ms',
            'omnivamp' => 'omnivamp_percent',
            'physicalVamp' => 'physical_vamp',
            'powerMax' => 'mana',
            'powerRegen' => 'mana_regen',
            'spellVamp' => 'spell_vamp_percent',
        ];
    }
}
