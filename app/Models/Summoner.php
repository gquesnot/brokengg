<?php

namespace App\Models;

use App\Data\FiltersData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Summoner
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $profile_icon_id
 * @property int|null $revision_date
 * @property int|null $summoner_level
 * @property string|null $last_scanned_match
 * @property bool $complete
 * @property string|null $summoner_id
 * @property string|null $account_id
 * @property string|null $puuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $auto_update
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerMatch[] $matches
 * @property-read int|null $matches_count
 * @method static Builder|Summoner newModelQuery()
 * @method static Builder|Summoner newQuery()
 * @method static Builder|Summoner query()
 * @method static Builder|Summoner whereAccountId($value)
 * @method static Builder|Summoner whereAutoUpdate($value)
 * @method static Builder|Summoner whereComplete($value)
 * @method static Builder|Summoner whereCreatedAt($value)
 * @method static Builder|Summoner whereId($value)
 * @method static Builder|Summoner whereLastScannedMatch($value)
 * @method static Builder|Summoner whereName($value)
 * @method static Builder|Summoner whereProfileIconId($value)
 * @method static Builder|Summoner wherePuuid($value)
 * @method static Builder|Summoner whereRevisionDate($value)
 * @method static Builder|Summoner whereSummonerId($value)
 * @method static Builder|Summoner whereSummonerLevel($value)
 * @method static Builder|Summoner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Summoner extends Model
{

    protected $fillable = [
        'name',
        'profile_icon_id',
        'revision_date',
        'summoner_level',
        'complete',
        'summoner_id',
        'account_id',
        'puuid',
    ];

    protected $casts = [
        'complete' => 'boolean',
    ];


    public function getMatchesCache(FiltersData $filters)
    {
        $matchesCount = $this->getMatchesCount($filters);
        $cache_key = $this->getCacheKey('matches', $filters, $matchesCount);
        return Cache::remember($cache_key, 60 * 24, function () use ($filters) {
            $matches_ids = $this->getMatchesQuery($filters)
                ->pluck('id');
            if (!$filters->filter_encounters) {
                $match_encounter_ids = SummonerMatch::whereSummonerId($this->id)->toBase()->pluck('match_id');
            }
            else{
                $match_encounter_ids = $matches_ids;
            }
            return [
                "match_ids" => $matches_ids,
                "encounters" => $this->encounters($match_encounter_ids)->pluck('total', 'summoner_id'),
            ];
        });
    }

    public function getSummonerMatchesIds(?FiltersData $filters = null, bool $applyFilters = true)
    {
        $query = SummonerMatch::whereSummonerId($this->id);
        if ($applyFilters && $filters && $filters->champion) {
            $query->whereChampionId($filters->champion);
        }
        return $query->pluck('match_id');
    }


    public function getCacheKey($cacheName, ?FiltersData $filters, $count)
    {
        $result = "{$cacheName}_{$count}";
        if ($filters) {
            foreach ($filters->toArray() as $key => $value) {
                $result .= "_{$key}_{$value}";
            }
        }

        return $result . "_{$this->id}";
    }

    public function getSummonerMatchesFiltered(FiltersData $filters = null, $limit = null)
    {
        $query = SummonerMatch::whereSummonerId($this->id);
        if ($filters?->champion) {
            $query->whereChampionId($filters->champion);
        }
        if ($limit != null) {
            $query = $query->limit($limit);
        }
        return $query->toBase()->pluck('match_id');
    }


    public function encounters($matchIds)
    {
        return SummonerMatch::whereIn('match_id', $matchIds)
            ->where('summoner_id', '!=', $this->id)
            ->selectRaw('summoner_id, count(*) as total')
            ->groupBy('summoner_id')
            ->orderByDesc('total')
            ->toBase();
    }

    public function getVersusMatchIds($meId, $otherId, $limit)
    {
        $query = "SELECT match_id FROM summoner_matches
                        WHERE summoner_id = {$meId} AND match_id IN
                         (SELECT match_id FROM summoner_matches WHERE summoner_id = {$otherId})";
        if ($limit != null) {
            $query .= " LIMIT {$limit}";
        }

        return collect(DB::select($query))->pluck('match_id');
    }

    public function versus($other, FiltersData $filters, $limit = null)
    {
        $meId = $this->id;
        $otherId = $other->id;
        $versusMatchIds = $this->getVersusMatchIds($meId, $otherId, $limit);

        $matches = Matche::whereIn('id', $versusMatchIds)
            ->filters($filters)
            ->select(['id', 'match_creation', 'match_duration', 'match_id', 'map_id', 'mode_id', 'match_end', 'queue_id'])
            ->with('participants.champion:id,name,img_url', 'mode:id,name', 'queue:id,description')
            ->withWhereHas('participants', function ($query) use ($meId, $otherId) {
                $query->where('summoner_id', $meId)->orWhere('summoner_id', $otherId);
            })
            ->orderBy('match_creation', 'DESC');

        $matches = $matches->get();

        return $matches->map(function (Matche $match) use ($meId, $otherId) {
            $match->setAttribute('me', $match->participants->filter(function ($participant) use ($meId) {
                return $participant->summoner_id == $meId;
            })->first());
            $match->setAttribute('other', $match->participants->filter(function ($participant) use ($otherId) {
                return $participant->summoner_id == $otherId;
            })->first());

            unset($match->participants);

            return $match;
        })->filter(function ($match) use ($filters) {
            if ($filters->champion) {
                return $match->me->champion_id == $filters->champion;
            }

            return true;
        });
    }

    public function getMatchesCount(FiltersData $filters): int
    {
        return $this->getMatchesQuery($filters)
            ->count();
    }

    public function getMatchesQuery( FiltersData$filters){
        $query=  SummonerMatch::whereSummonerId($this->id);

        if ($filters->champion){
            $query->whereChampionId($filters->champion);
        }
        $matchIds = $query->toBase()->pluck('match_id')->toArray();
        return Matche::whereIn('id', $matchIds)->whereUpdated(true)->filters($filters)->orderByDesc('match_creation')->toBase();
    }

    public function champions()
    {
        return Champion::with(['matches' => function ($query) {
            $query->where('summoner_id', $this->id);
        }], 'matches.match');
    }

    public function champion($championId)
    {
        return Champion::where('id', $championId)->with(['matches' => function ($query) {
            $query->where('summoner_id', $this->id);
        }])->first();
    }

    public function matches()
    {
        return $this->hasMany(SummonerMatch::class, 'summoner_id', 'id')->with([
            'champion', 'match', 'match.mode', 'match.queue', 'match.map', 'items', 'match.participants', 'match.participants.champion', 'match.participants.summoner',
        ]);
    }
}
