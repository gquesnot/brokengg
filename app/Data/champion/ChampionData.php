<?php

namespace App\Data\champion;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ChampionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $img_url,
        public ChampionStats $stats,
    ) {
    }
}
