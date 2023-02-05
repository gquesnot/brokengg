<?php

namespace App\Enums;

use App\Traits\EnumTrait;
use Illuminate\Support\Facades\Storage;

enum Tier: string
{
    use EnumTrait;

    case IRON = 'iron';
    case BRONZE = 'bronze';
    case SILVER = 'silver';
    case GOLD = 'gold';
    case PLATINUM = 'platinum';
    case DIAMOND = 'diamond';
    case MASTER = 'master';
    case GRANDMASTER = 'grandmaster';
    case CHALLENGER = 'challenger';

    public function rankName(): string
    {
        return match ($this) {
            self::IRON => 'Iron',
            self::BRONZE => 'Bronze',
            self::SILVER => 'Silver',
            self::GOLD => 'Gold',
            self::PLATINUM => 'Platinum',
            self::DIAMOND => 'Diamond',
            self::MASTER => 'Master',
            self::GRANDMASTER => 'Grandmaster',
            self::CHALLENGER => 'Challenger',
        };
    }

    public function url(): string
    {
        return Storage::disk('local')->url('ranks/emblem-'.$this->value.'.png');
    }
}
