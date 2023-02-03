<?php

namespace App\Data\champion;

use App\Traits\JsonCastTrait;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ChampionAbility extends Data
{
    use JsonCastTrait;

    public string $name;

    public string $key;

    public string $description;

    public string $icon_url;

    #[DataCollectionOf(ChampionAbilityLevel::class)]
    public ?DataCollection $levels;
}
