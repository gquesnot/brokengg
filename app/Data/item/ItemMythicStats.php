<?php

namespace App\Data\item;

use App\Data\DataJsonCast;
use Illuminate\Support\Arr;
use Livewire\Wireable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ItemMythicStats extends DataJsonCast implements Wireable
{
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

    public static function from_api($datas): ItemMythicStats
    {
        $stats = new self();

        $stats->hp = Arr::get($datas, 'health', 0);
        $stats->hp_percent = Arr::get($datas, 'health_percent', 0);
        $stats->ah = Arr::get($datas, 'ability_haste', 0);
        $stats->ms = Arr::get($datas, 'movement_speed', 0);
        $stats->ms_percent = Arr::get($datas, 'movement_speed_percent', 0);
        $stats->omnivamp_percent = Arr::get($datas, 'omnivamp_percent', 0);
        $stats->heal_power_percent = Arr::get($datas, 'increased_heal', 0);
        $stats->as_percent = Arr::get($datas, 'attack_speed_percent', 0);
        $stats->ap = Arr::get($datas, 'ability_power', 0);
        $stats->ad = Arr::get($datas, 'attack_damage', 0);
        $stats->armor = Arr::get($datas, 'armor', 0);
        $stats->mr = Arr::get($datas, 'magic_resistance', 0);
        $stats->armor_pen_percent = Arr::get($datas, 'armor_penetration_percent', 0);
        $stats->armor_pen_flat = Arr::get($datas, 'lethality', 0);
        $stats->magic_pen_percent = Arr::get($datas, 'magic_penetration_percent', 0);
        $stats->magic_pen_flat = Arr::get($datas, 'magic_penetration', 0);

        return $stats;
    }
}
