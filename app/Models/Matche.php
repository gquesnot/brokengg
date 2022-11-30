<?php

namespace App\Models;

use App\Data\FiltersData;
use App\Data\match_timeline\ParticipantData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property string|null $match_creation
 * @property string|null $match_duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Spatie\LaravelData\DataCollection|null $details
 * @property-read mixed $since_match_end
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
 * @method static Builder|Matche whereDetails($value)
 * @method static Builder|Matche whereId($value)
 * @method static Builder|Matche whereMapId($value)
 * @method static Builder|Matche whereMatchCreation($value)
 * @method static Builder|Matche whereMatchDuration($value)
 * @method static Builder|Matche whereMatchId($value)
 * @method static Builder|Matche whereModeId($value)
 * @method static Builder|Matche whereQueueId($value)
 * @method static Builder|Matche whereUpdated($value)
 * @method static Builder|Matche whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Matche extends Model
{
    use HasFactory;

    public $appends = ['since_match_end'];

    protected $fillable = [
        'match_id',
        'mode_id',
        'queue_id',
        'map_id',
        'match_creation',
        'match_duration',
        'details',
    ];

    public $casts = [
        'details' => ParticipantData::class,
    ];

    public function getSinceMatchEndAttribute()
    {
        // convert 00:00:00 to h m s
        $match_duration = explode(':', $this->getRawOriginal('match_duration'));
        $match_duration_m = intval($match_duration[0]) * 60 + intval($match_duration[1]);
        $match_duration_s = intval($match_duration[2]);

        return Carbon::parse($this->match_creation)
            ->addMinutes($match_duration_m)
            ->addSeconds($match_duration_s)
            ->diffForHumans();
    }

    public function getMatchDurationAttribute($value)
    {
        $value = explode(':', $value);

        $match_h = intval($value[0]);
        $match_m = intval($value[1]);
        $match_s = intval($value[2]);

        $res = '';
        if ($match_h > 0) {
            $res .= $match_h.'h ';
        }
        $res .= $match_m.'m ';
        $res .= $match_s.'s';

        return $res;
    }

    public function scopeFilters(Builder $query, ?FiltersData $filters): Builder
    {
        if ($filters) {
            if ($filters->date_start) {
                $query->where('match_creation', '>=', $filters->date_start);
            }
            if ($filters->date_end) {
                $query->where('match_creation', '<=', $filters->date_end);
            }
            if ($filters->queue) {
                $query->where('queue_id', $filters->queue);
            }
        }

        return $query;
    }

    public function participants()
    {
        return $this->hasMany(SummonerMatch::class, 'match_id', 'id');
    }

    public function mode()
    {
        return $this->hasOne(Mode::class, 'id', 'mode_id');
    }

    public function queue()
    {
        return $this->hasOne(Queue::class, 'id', 'queue_id');
    }

    public function map()
    {
        return $this->hasOne(Map::class, 'id', 'map_id');
    }
}
