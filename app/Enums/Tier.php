<?php

namespace App\Enums;

use App\Traits\EnumTrait;
use Illuminate\Support\Facades\Storage;

enum Tier: string
{
    use EnumTrait;

    case IRON = 'IRON';
    case BRONZE = 'BRONZE';
    case SILVER = 'SILVER';
    case GOLD = 'GOLD';
    case PLATINUM = 'PLATINUM';
    case DIAMOND = 'DIAMOND';
    case MASTER = 'MASTER';
    case GRANDMASTER = 'GRANDMASTER';
    case CHALLENGER = 'CHALLENGER';

    public function name(): string
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

    public function number()
    {
        return match ($this) {
            self::IRON => 1,
            self::BRONZE => 5,
            self::SILVER => 9,
            self::GOLD => 13,
            self::PLATINUM => 17,
            self::DIAMOND => 21,
            self::MASTER => 25,
            self::GRANDMASTER => 26,
            self::CHALLENGER => 27,
        };
    }

    public static function tierFromNumber(int $number): Tier
    {
        return match ($number) {
            1, 2, 3, 4 => self::IRON,
            5, 6, 7, 8 => self::BRONZE,
            9, 10, 11, 12 => self::SILVER,
            13, 14, 15, 16 => self::GOLD,
            17, 18, 19, 20 => self::PLATINUM,
            21, 22, 23, 24 => self::DIAMOND,
            25 => self::MASTER,
            26 => self::GRANDMASTER,
            27 => self::CHALLENGER,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::IRON => 'rgba(255, 255, 255, 0.5)',
            self::BRONZE => 'rgba(192, 192, 192, 0.5)',
            self::SILVER => 'rgba(255, 255, 0, 0.5)',
            self::GOLD => 'rgba(255, 215, 0, 0.5)',
            self::PLATINUM => 'rgba(255, 165, 0, 0.5)',
            self::DIAMOND => 'rgba(255, 0, 0, 0.5)',
            self::MASTER => 'rgba(128, 0, 128, 0.5)',
            self::GRANDMASTER => 'rgba(0, 0, 255, 0.5)',
            self::CHALLENGER => 'rgba(0, 255, 0, 0.5)',
        };
    }

    public function url(): string
    {
        return Storage::disk('local')->url('ranks/emblem-'.$this->value.'.png');
    }
}
