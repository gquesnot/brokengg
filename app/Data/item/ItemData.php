<?php

namespace App\Data\item;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\WithData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ItemData extends Data
{
    use WithData;

    protected $dataClass = ItemData::class;

    public function __construct(
        public int $id,
        public int $gold,
        public string $name,
        public string $description,
        public array $tags,
        public string $img_url,
        public string $type,
        public string $colloq,
        public ItemStats $stats,
        public ?ItemMythicStats $mythic_stats = null,

    ) {
    }
}
