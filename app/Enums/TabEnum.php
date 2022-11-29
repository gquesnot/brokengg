<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum TabEnum: string
{
    use EnumTrait;

    case MATCHES = 'matches';
    case CHAMPIONS = 'champions';
    case ENCOUNTERS = 'encounters';
    case LIVE_GAME = 'live_game';
    case VERSUS = 'versus';
    case MATCH_DETAIL = 'match_detail';

    public function only_summoner_id(): bool
    {
        return in_array($this, [
            self::MATCHES,
            self::CHAMPIONS,
            self::ENCOUNTERS,
            self::LIVE_GAME,
        ]);
    }

    public function title(): string
    {
        return match($this){
            self::MATCHES => 'Matches',
            self::CHAMPIONS => 'Champions',
            self::ENCOUNTERS => 'Encounters',
            self::LIVE_GAME => 'Live Game',
            self::VERSUS => 'Versus',
            self::MATCH_DETAIL => 'Match Detail',
        };
    }


}
