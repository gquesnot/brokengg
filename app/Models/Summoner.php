<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Summoner
 *
 * @property int $id
 * @property string|null $summoner_id
 * @property string|null $account_id
 * @property string|null $puuid
 * @property string|null $name
 * @property string|null $profile_icon_id
 * @property string|null $revision_date
 * @property string|null $summoner_level
 * @property string|null $last_scanned_match
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $auto_update
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerMatch[] $matches
 * @property-read int|null $matches_count
 *
 * @method static Builder|Summoner newModelQuery()
 * @method static Builder|Summoner newQuery()
 * @method static Builder|Summoner query()
 * @method static Builder|Summoner whereAccountId($value)
 * @method static Builder|Summoner whereAutoUpdate($value)
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
    use HasFactory;

    protected $fillable = [
        'summoner_id',
        'account_id',
        'puuid',
        'name',
        'profile_icon_id',
        'revision_date',
        'summoner_level',
    ];

    public function getCachedMatchesQuery($filters = [])
    {
        $count = $this->getMatchesQuery($filters)->count();

        return Cache::remember($this->getCacheKey('matche_ids', $filters, $count), 60 * 5, function () use ($filters) {
            return $this->getMatchesQuery($filters)->pluck('id');
        });
    }

    public function getMatchesQuery($filters = null, $limit = null): Builder
    {
        $query = Matche::whereHas('participants', function ($query) use ($filters) {
            $query->where('summoner_id', $this->id);
            if (Arr::get($filters, 'champion') != null) {
                $query->whereChampionId($filters['champion']);
            }
        })->filters($filters)->orderByDesc('match_creation');

        if ($limit != null) {
            $query = $query->limit($limit);
        }

        return $query;
    }

    public function getCacheKey($cacheName, $filters, $count)
    {
        $result = "{$cacheName}_{$count}";
        foreach ($filters as $key => $value) {
            $result .= "_{$key}_{$value}";
        }

        return $result."_{$this->id}";
    }

    public function getCachedEncounters($matchIds, $filters = [])
    {
        return Cache::remember($this->getCacheKey('encounters', $filters, $matchIds->count()), 60 * 5, function () use ($matchIds) {
            return $this->encounters($matchIds)->pluck('total', 'summoner_id');
        });
    }

    public function encounters($matchIds = null)
    {
        return SummonerMatch::whereIn('match_id', $matchIds)
            ->where('summoner_id', '!=', $this->id)
            ->selectRaw('summoner_id, count(*) as total')
            ->groupBy('summoner_id')
            ->orderByDesc('total')
            ->get();
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

    public function versus($other, $filters, $limit = null)
    {
        $meId = $this->id;
        $otherId = $other->id;
        $versusMatchIds = $this->getVersusMatchIds($meId, $otherId, $limit);

        $matches = Matche::whereIn('id', $versusMatchIds)
            ->filters($filters)
            ->select(['id', 'match_creation', 'match_id', 'map_id', 'mode_id'])
            ->with('participants.champion:id,name,img_url', 'mode:id,name')
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
            if (Arr::get($filters, 'champion') != null) {
                return $match->me->champion_id == $filters['champion'];
            }

            return true;
        });
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
