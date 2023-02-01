<?php

namespace App\Data\match_timeline;

use App\Data\champion\ChampionData;
use App\Models\SummonerMatch;
use App\Traits\WireableData;
use Arr;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ParticipantData extends Data
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
        public PerksData $perks,
    ) {
    }

    public static function fromApi(SummonerMatch $participant, int $index, array $match_timeline)
    {
        $destroyed = collect([]);
        $undo = collect([]);
        $sold = collect([]);

        $frames = collect($match_timeline['frames'])->map(function ($frame) use ($index, $destroyed, $undo, $sold) {
            $participant_frame = collect($frame['participantFrames'])->firstWhere('participantId', $index);
            $events = collect($frame['events'])->filter(function ($event) use ($index, $destroyed, $undo, $sold) {
                return Arr::get($event, 'participantId', 0) == $index && Str::contains($event['type'], 'ITEM');
            })->map(function ($event) use ($destroyed, $undo, $sold) {
                if ($event['type'] == "ITEM_DESTROYED"){
                    $destroyed->push($event);
                }
                if ($event['type'] == "ITEM_UNDO"){
                    $undo->push($event);
                }
                if ($event['type'] == "ITEM_SOLD"){
                    $sold->push($event);
                }
                return ShopEventData::from(
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

            return FrameData::from([
                'events' => $events,
                'stats' => ParticipantFrameStatsData::mapping($participant_frame['championStats']),
                'total_gold' => $participant_frame['totalGold'],
                'current_gold' => $participant_frame['currentGold'],
                'level' => $participant_frame['level'],

            ]);
        });
//        dd($destroyed, $undo, $sold);
        return ParticipantData::from([
            'id' => $index,
            'name' => $participant->summoner->name,
            'puuid' => $participant->summoner->puuid,
            'profile_icon_id' => $participant->summoner->profile_icon_id,
            'won' => $participant->won,
            'champion' => $participant->champion->getData(),
            'frames' => $frames,
            'perks' => $participant->perks,
        ]);
    }
}
