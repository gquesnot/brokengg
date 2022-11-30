<?php

namespace App\Data\item;

use App\Data\DataJsonCast;
use Illuminate\Support\Arr;
use Livewire\Wireable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ItemStats extends DataJsonCast implements Wireable
{
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

    public static function from_api($datas): ItemStats
    {
        $result = new self();
        $result->ms = Arr::get($datas, 'move speed', 0);
        $result->hp = Arr::get($datas, 'health', 0);
        $result->crit_percent = Arr::get($datas, 'critical strike chance percent', 0);
        $result->ap = Arr::get($datas, 'ability power', 0);
        $result->mp = Arr::get($datas, 'mana', 0);
        $result->armor = Arr::get($datas, 'armor', 0);
        $result->mr = Arr::get($datas, 'magic resist', 0);
        $result->ad = Arr::get($datas, 'attack damage', 0);
        $result->as_percent = Arr::get($datas, 'attack speed percent', 0);
        $result->life_steal_percent = Arr::get($datas, 'life steal percent', 0);
        $result->ah = Arr::get($datas, 'ability haste', 0);
        $result->mp_regen_percent = Arr::get($datas, 'base mana regen percent', 0);
        $result->flat_magic_pen = Arr::get($datas, 'magic penetration', 0);
        $result->ms_percent = Arr::get($datas, 'move speed percent', 0);
        $result->armor_pen_percent = Arr::get($datas, 'armor penetration percent', 0);
        $result->hp_regen_percent = Arr::get($datas, 'base health regen percent', 0);
        $result->omnivamp_percent = Arr::get($datas, 'omnivamp percent', 0);
        $result->heal_power_percent = Arr::get($datas, 'heal and shield power percent', 0);
        $result->tenacity_percent = Arr::get($datas, 'tenacity percent', 0);
        $result->magic_pen_percent = Arr::get($datas, 'magic penetration percent', 0);
        $result->flat_armor_pen = Arr::get($datas, 'lethality', 0);
        $result->toJson();

        return $result;
    }
}
