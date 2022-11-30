<?php

namespace App\Data\match_timeline;

use App\Data\DataJsonCast;
use Livewire\Wireable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ShopEventData extends DataJsonCast implements Wireable
{
    public function __construct(
        public string $type,
        public int $timestamp,
        public int $participant_id,
        public ?int $item_id = null,
        public ?int $gold_gain = null,
        public ?int $after_id = null,
        public ?int $before_id = null,
    ) {
    }
}
