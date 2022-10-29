<?php

namespace App\Models;

use Carbon\Carbon;
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
 * @property-read mixed $since_match_end
 * @property-read \App\Models\Map|null $map
 * @property-read \App\Models\Mode|null $mode
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerMatch[] $participants
 * @property-read int|null $participants_count
 * @property-read \App\Models\Queue|null $queue
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Matche newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Matche newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Matche query()
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereMapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereMatchCreation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereMatchDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereModeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereQueueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Matche whereUpdatedAt($value)
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
    ];

    public function getSinceMatchEndAttribute()
    {
        $duration = Carbon::parse($this->getRawOriginal('match_duration'));

        return Carbon::parse($this->match_creation)
            ->addHours($duration->hour)
            ->addMinutes($duration->minute)
            ->addSeconds($duration->second)
            ->diffForHumans();
    }

    public function getMatchDurationAttribute($value)
    {
        $duration = Carbon::parse($value);
        $res = '';
        if ($duration->hour > 0) {
            $res .= $duration->hour.'h ';
        }
        $res .= $duration->minute.'m ';
        $res .= $duration->second.'s';

        return $res;
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
