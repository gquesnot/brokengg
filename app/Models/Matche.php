<?php

namespace App\Models;

use App\Data\FiltersData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Matche
 *
 * @property int $id
 * @property int $updated
 * @property string $match_id
 * @property int|null $mode_id
 * @property int|null $map_id
 * @property int|null $queue_id
 * @property \Illuminate\Support\Carbon|null $match_creation
 * @property \Illuminate\Support\Carbon|null $match_end
 * @property \Illuminate\Support\Carbon|null $match_duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Map|null $map
 * @property-read \App\Models\Mode|null $mode
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerMatch[] $participants
 * @property-read int|null $participants_count
 * @property-read \App\Models\Queue|null $queue
 *
 * @method static Builder|Matche filters(?\App\Data\FiltersData $filters)
 * @method static Builder|Matche newModelQuery()
 * @method static Builder|Matche newQuery()
 * @method static Builder|Matche query()
 * @method static Builder|Matche whereCreatedAt($value)
 * @method static Builder|Matche whereId($value)
 * @method static Builder|Matche whereMapId($value)
 * @method static Builder|Matche whereMatchCreation($value)
 * @method static Builder|Matche whereMatchDuration($value)
 * @method static Builder|Matche whereMatchEnd($value)
 * @method static Builder|Matche whereMatchId($value)
 * @method static Builder|Matche whereModeId($value)
 * @method static Builder|Matche whereQueueId($value)
 * @method static Builder|Matche whereUpdated($value)
 * @method static Builder|Matche whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Matche extends Model
{
    protected $fillable = [
        'match_id',
        'mode_id',
        'queue_id',
        'map_id',
        'match_creation',
        'match_end',
        'match_duration',
    ];

    public $dates = [
        'match_creation',
        'match_end',
        'match_duration',
    ];

    public function sinceMatchEnd()
    {
        return $this->match_end->diffForHumans(Carbon::now());
    }

    public function scopeFilters(Builder $query, ?FiltersData $filters): Builder
    {
        if ($filters) {
            if ($filters->date_start) {
                $query->whereDate('match_creation', '>=', $filters->date_start);
            }
            if ($filters->date_end) {
                $query->whereDate('match_creation', '<=', $filters->date_end);
            }
            if ($filters->queue) {
                $query->where('queue_id', $filters->queue);
            }
        }

        return $query;
    }

    public function participants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SummonerMatch::class, 'match_id', 'id');
    }

    public function mode(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Mode::class, 'id', 'mode_id');
    }

    public function queue(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Queue::class, 'id', 'queue_id');
    }

    public function map()
    {
        return $this->hasOne(Map::class, 'id', 'map_id');
    }
}
