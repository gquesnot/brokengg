<?php

namespace App\Data\champion;

use App\Data\DataJsonCast;
use Livewire\Wireable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
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
