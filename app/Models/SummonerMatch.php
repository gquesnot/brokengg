<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SummonerMatch
 *
 * @property int $id
 * @property int $won
 * @property int $champion_id
 * @property float $kill_participation
 * @property float $kda
 * @property int $assists
 * @property int $deaths
 * @property int $kills
 * @property int $champ_level
 * @property array|null $challenges
 * @property array $stats
 * @property int $minions_killed
 * @property int $largest_killing_spree
 * @property int $summoner_id
 * @property int $match_id
 * @property int $double_kills
 * @property int $triple_kills
 * @property int $quadra_kills
 * @property int $penta_kills
 * @property-read \App\Models\Champion|null $champion
 * @property-read mixed $cs_per_minute
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Item[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Matche|null $match
 * @property-read \App\Models\Summoner|null $summoner
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereAssists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereChallenges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereChampLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereChampionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereDeaths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereDoubleKills($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereKda($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereKillParticipation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereKills($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereLargestKillingSpree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereMinionsKilled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch wherePentaKills($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereQuadraKills($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereSummonerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereTripleKills($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerMatch whereWon($value)
 * @mixin Eloquent
 * @mixin IdeHelperSummonerMatch
 */
class SummonerMatch extends Model
{
    use HasFactory;

    public $appends = ['csPerMinute'];

    public $timestamps = false;

    protected $fillable = [
        'summoner_id',
        'match_id',
        'won',
        'kda',
        'kill_participation',
        'kills',
        'deaths',
        'assists',
        'champion_id',
        'champ_level',
        'challenges',
        'stats',
        'minions_killed',
        'largest_killing_spree',
        'double_kills',
        'triple_kills',
        'quadra_kills',
        'penta_kills',
    ];

    protected $casts = [
        'stats' => 'array',
        'challenges' => 'array',
    ];

    public function getCsPerMinuteAttribute()
    {
        $minutes = Carbon::createFromTimeString($this->match->getRawOriginal('match_duration'))->minute;
        if ($minutes > 0) {
            return round($this->minions_killed / $minutes, 1);
        } else {
            return $this->minions_killed;
        }
    }

    public function items()
    {
        return $this->hasManyThrough(Item::class, ItemSummonerMatch::class, 'summoner_match_id', 'id', 'id', 'item_id')->orderBy('position');
    }

    public function summoner()
    {
        return $this->hasOne(Summoner::class, 'id', 'summoner_id');
    }

    public function match()
    {
        return $this->hasOne(Matche::class, 'id', 'match_id');
    }

    public function champion()
    {
        return $this->hasOne(Champion::class, 'id', 'champion_id');
    }
}
