<?php

namespace App\Data\RiotApi\LiveGame;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class GameCustomizationData extends Data
{
    public function __construct(
        public string $category,
        public string $content,
    )
    {
    }
}
