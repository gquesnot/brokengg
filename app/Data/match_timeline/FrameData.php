<?php

namespace App\Data\match_timeline;

use App\Data\DataJsonCast;
use Livewire\Wireable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FrameData extends DataJsonCast implements Wireable
{
    public function __construct(
        #[DataCollectionOf(ShopEventData::class)]
        public DataCollection $events,
        public ParticipantFrameStatsData $stats,
        public int $total_gold,
        public int $current_gold,
        public int $level
    ) {
    }

    public static function from_model($item)
    {
        $events = new DataCollection(ShopEventData::class, collect($item['events'])->map(function ($event) {
            return ShopEventData::withoutMagicalCreationFrom($event);
        })->values());
        $stats = ParticipantFrameStatsData::withoutMagicalCreationFrom($item['stats']);

        return new self(
            $events,
            $stats,
            $item['total_gold'],
            $item['current_gold'],
            $item['level'],
        );
    }
}
