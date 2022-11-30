<?php

namespace App\Data\item;

use App\interfaces\DataMappingInterface;
use App\Traits\DataMappingTrait;
use App\Traits\JsonCastTrait;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ItemMythicStats extends Data implements DataMappingInterface
{
    use DataMappingTrait, JsonCastTrait;

    public function __construct(
        public int $hp = 0,
        public int $ah = 0,
        public int $ms = 0,
        public int $ap = 0,
        public int $ad = 0,
        public int $armor = 0,
        public int $mr = 0,
        public int $armor_pen_flat = 0,
        public int $magic_pen_flat = 0,
        public float $hp_percent = 0,
        public float $armor_pen_percent = 0,
        public float $magic_pen_percent = 0,
        public float $ms_percent = 0,
        public float $omnivamp_percent = 0,
        public float $heal_power_percent = 0,
        public float $as_percent = 0,
    ) {
    }

    public static function getMapping(): array
    {
        return [
            'health' => 'hp',
            'health_percent' => 'hp_percent',
            'ability_haste' => 'ah',
            'movement_speed' => 'ms',
            'movement_speed_percent' => 'ms_percent',
            'omnivamp_percent' => 'omnivamp_percent',
            'increased_heal' => 'heal_power_percent',
            'attack_speed_percent' => 'as_percent',
            'ability_power' => 'ap',
            'attack_damage' => 'ad',
            'armor' => 'armor',
            'magic_resistance' => 'mr',
            'armor_penetration_percent' => 'armor_pen_percent',
            'lethality' => 'armor_pen_flat',
            'magic_penetration_percent' => 'magic_pen_percent',
            'magic_penetration' => 'magic_pen_flat',
        ];
    }
}
