<?php

namespace App\Data\match_timeline;

use App\Data\champion\ChampionData;
use App\Data\DataCollectionJsonCast;
use App\Models\SummonerMatch;
use App\Traits\WireableData;
use Arr;
use Illuminate\Support\Str;
use Livewire\Wireable;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ParticipantData extends DataCollectionJsonCast implements Wireable
{
    use WireableData;

    public function __construct(
        public int $id,
        public string $name,
        public string $puuid,
        public int $profile_icon_id,
        public bool $won,
        public ChampionData $champion,
        #[DataCollectionOf(FrameData::class)]
        public DataCollection $frames,
    ) {
    }

    public static function from_model(array $model)
    {
        $champion = ChampionData::from_model($model['champion']);
        $frames = new DataCollection(FrameData::class, collect($model['frames'])->map(function ($item) {
            return FrameData::from_model($item);
        })->values());

        return new self(
            $model['id'],
            $model['name'],
            $model['puuid'],
            $model['profile_icon_id'],
            $model['won'],
            $champion,
            $frames,
        );
    }

    public static function from_api(SummonerMatch $participant, int $index, array $match_timeline)
    {
        $frames = collect($match_timeline['frames'])->map(function ($frame) use ($index) {
            $participant_frame = collect($frame['participantFrames'])->firstWhere('participantId', $index);
            $events = collect($frame['events'])->filter(function ($event) use ($index) {
                return Arr::get($event, 'participantId', 0) == $index && Str::contains($event['type'], 'ITEM');
            })->map(function ($event) {
                return ShopEventData::withoutMagicalCreationFrom(
                    [
                        'type' => $event['type'],
                        'timestamp' => $event['timestamp'],
                        'participant_id' => $event['participantId'],
                        'item_id' => $event['itemId'] ?? null,
                        'gold_gain' => $event['goldGain'] ?? null,
                        'after_id' => $event['afterId'] ?? null,
                        'before_id' => $event['beforeId'] ?? null,
                    ],
                );
            })->values();

            return FrameData::withoutMagicalCreationFrom([
                'events' => $events,
                'stats' => ParticipantFrameStatsData::from_api($participant_frame['championStats']),
                'total_gold' => $participant_frame['totalGold'],
                'current_gold' => $participant_frame['currentGold'],
                'level' => $participant_frame['level'],

            ]);
        });

        return ParticipantData::from([
            'id' => $index,
            'name' => $participant->summoner->name,
            'puuid' => $participant->summoner->puuid,
            'profile_icon_id' => $participant->summoner->profile_icon_id,
            'won' => $participant->won,
            'champion' => ChampionData::from_model($participant->champion->toArray()),
            'frames' => $frames,
        ]);
    }
}
