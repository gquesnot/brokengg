<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereLastScannedMatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereProfileIconId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner wherePuuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereRevisionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereSummonerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereSummonerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Summoner whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperSummoner
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
            return $this->getMatchesQuery($filters)->select('id', 'match_creation')->pluck('id');
        });
    }

    public function getMatchesQuery($filters = null, $limit = null): Builder
    {
        $query = Matche::whereHas('participants', function ($query) {
            $query->where('summoner_id', $this->id);
        });

        $query = $this->applyFilters($query, $filters);
        $query = $query->orderByDesc('match_creation');
        if ($limit != null) {
            $query = $query->limit($limit);
        }

        return $query;
    }

    public function applyFilters($query, $filters)
    {
        if (! empty($filters)) {
            if ($filters['queue'] != null) {
                $query = $query->where('queue_id', $filters['queue']);
            }
            if ($filters['dateStart'] != null) {
                $query = $query->where('match_creation', '>=', $filters['dateStart']);
            }
            if ($filters['dateEnd'] != null) {
                $query = $query->where('match_creation', '<=', $filters['dateEnd']);
            }
            if ($filters['champion'] != null) {
                $query = $query->whereHas('participants', function ($query) use ($filters) {
                    $query->where('summoner_id', $this->id)->where('champion_id', $filters['champion']);
                });
            }
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

    public function versus($other, $filters, $limit = null)
    {
        $meId = $this->id;
        $otherId = $other->id;
        $query = "select match_id from summoner_matches where summoner_id = $meId and match_id in (select match_id from summoner_matches where summoner_id = $otherId)";
        if ($limit != null) {
            $query .= " limit $limit";
        }
        $results = collect(DB::select($query))->pluck('match_id');

        $matches = Matche::whereIn('id', $results)
            ->orderBy('match_creation', 'DESC');
        $matches = $this->applyFilters($matches, $filters);
        $matches = $matches
            ->with(['participants' => function ($query) use ($meId, $otherId) {
                $query->where('summoner_id', $meId)->orWhere('summoner_id', $otherId);
            }])
            ->with('participants.champion', 'mode', 'queue', 'map')
            ->get();

        return $matches->map(function ($match) use ($meId, $otherId, $filters) {
            $match['me'] = $match->participants->filter(function ($participant) use ($meId) {
                return $participant->summoner_id == $meId;
            })->first();
            $match['other'] = $match->participants->filter(function ($participant) use ($otherId) {
                return $participant->summoner_id == $otherId;
            })->first();
            if ($filters != null && $filters['champion'] != null) {
                if ($match['me']->champion_id != $filters['champion']) {
                    return null;
                }
            }
            unset($match->participants);

            return $match;
        })->filter(function ($match) {
            return $match != null;
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
