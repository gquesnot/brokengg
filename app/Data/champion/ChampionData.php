<?php

namespace App\Data\champion;

use App\Data\DataJsonCast;
use Livewire\Wireable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ChampionData extends DataJsonCast implements Wireable
{
    public function __construct(
        public int $id,
        public string $name,
        public string $img_url,
        public ChampionStats $stats,
    ) {
    }
}
