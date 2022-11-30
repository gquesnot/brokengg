<?php

namespace App\Data\match_timeline;

use App\interfaces\DataMappingInterface;
use App\Traits\DataMappingTrait;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ShopEventData extends Data implements DataMappingInterface
{
    use DataMappingTrait;

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

    public static function getMapping(): array
    {
        return [
            'type' => 'type',
            'timestamp' => 'timestamp',
            'participantId' => 'participant_id',
            'itemId' => 'item_id',
            'goldGain' => 'gold_gain',
            'afterId' => 'after_id',
            'beforeId' => 'before_id',
        ];
    }
}
