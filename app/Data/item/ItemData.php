<?php

namespace App\Data\item;

use App\Data\DataJsonCast;
use Livewire\Wireable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ItemData extends DataJsonCast implements Wireable
{
    public function __construct(
        public int $id,
        public int $gold,
        public string $name,
        public string $description,
        public array $tags,
        public string $img_url,
        public string $type,
        public string $colloq,
        public ?ItemMythicStats $mythic_stats = null,
        public ?ItemStats $stats = null,
    ) {
    }

    public static function from_model(\App\Models\Item $item): self
    {
        return new self(
            $item->id,
            $item->gold,
            $item->name,
            $item->description,
            $item->tags,
            $item->img_url,
            $item->type,
            $item->colloq,
            $item->mythic_stats,
            $item->stats ? ItemStats::withoutMagicalCreationFrom($item->stats) : null,
        );
    }
}
