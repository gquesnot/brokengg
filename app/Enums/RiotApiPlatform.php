<?php

namespace App\Enums;

enum RiotApiPlatform: string
{
    case BR1 = 'br1';
    case EUN1 = 'eun1';
    case EUW1 = 'euw1';
    case JP1 = 'jp1';
    case KR = 'kr';
    case LA1 = 'la1';
    case LA2 = 'la2';
    case NA1 = 'na1';
    case OC1 = 'oc1';
    case TR1 = 'tr1';
    case RU = 'ru';
    case PH2 = 'ph2';
    case SG2 = 'sg2';
    case TH2 = 'th2';
    case TW2 = 'tw2';
    case VN2 = 'vn2';


    public function getRegion(): RiotApiRegion
    {
        return match ($this->value) {
            self::BR1->value, self::LA1->value, self::LA2->value, self::NA1->value, self::OC1->value => RiotApiRegion::AMERICAS,
            self::EUN1->value, self::EUW1->value, self::TR1->value, self::RU->value => RiotApiRegion::EUROPE,
            self::JP1->value, self::KR->value => RiotApiRegion::ASIA,
            self::PH2->value, self::TW2->value, self::SG2->value, self::TH2->value, self::VN2->value => RiotApiRegion::SEA,
        };
    }

}
