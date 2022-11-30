<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\SummonerMatch
 *
 * @property int $id
 * @property int $won
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
 * @property int $champion_id
 * @property int $summoner_id
 * @property int $match_id
 * @property int $double_kills
 * @property int $triple_kills
 * @property int $quadra_kills
 * @property int $penta_kills
 * @property-read \App\Models\Champion|null $champion
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Item[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Matche|null $match
 * @property-read \App\Models\Summoner|null $summoner
 *
 * @method static Builder|SummonerMatch championsCalc($championIds)
 * @method static Builder|SummonerMatch filters($filters)
 * @method static Builder|SummonerMatch newModelQuery()
 * @method static Builder|SummonerMatch newQuery()
 * @method static Builder|SummonerMatch query()
 * @method static Builder|SummonerMatch whereAssists($value)
 * @method static Builder|SummonerMatch whereChallenges($value)
 * @method static Builder|SummonerMatch whereChampLevel($value)
 * @method static Builder|SummonerMatch whereChampionId($value)
 * @method static Builder|SummonerMatch whereDeaths($value)
 * @method static Builder|SummonerMatch whereDoubleKills($value)
 * @method static Builder|SummonerMatch whereId($value)
 * @method static Builder|SummonerMatch whereKda($value)
 * @method static Builder|SummonerMatch whereKillParticipation($value)
 * @method static Builder|SummonerMatch whereKills($value)
 * @method static Builder|SummonerMatch whereLargestKillingSpree($value)
 * @method static Builder|SummonerMatch whereMatchId($value)
 * @method static Builder|SummonerMatch whereMinionsKilled($value)
 * @method static Builder|SummonerMatch wherePentaKills($value)
 * @method static Builder|SummonerMatch whereQuadraKills($value)
 * @method static Builder|SummonerMatch whereStats($value)
 * @method static Builder|SummonerMatch whereSummonerId($value)
 * @method static Builder|SummonerMatch whereTripleKills($value)
 * @method static Builder|SummonerMatch whereWon($value)
 * @mixin \Eloquent
 */
class SummonerMatch extends Model
{
    use HasFactory;

    //public $appends = ['cs_per_minute'];

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

    public function csPerMinute(): Attribute
    {
        return Attribute::make(
            get: function () {
                $minutes = Carbon::createFromTimeString($this->match->getRawOriginal('match_duration'))->minute;

                return $minutes > 0 ? round($this->minions_killed / $minutes) : $this->minions_killed;
            }
        );
    }

    public function winrate(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->wins / $this->total * 100, 2),
        );
    }

    public function loses(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total - $this->wins,
        );
    }

    public function avgKda(): Attribute
    {
        return Attribute::make(
            get: fn () => round(($this->avg_kills + $this->avg_assists) / $this->avg_deaths, 2),
        );
    }

    public function wins(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function avgKills(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => round(floatval($value), 2),
        );
    }

    public function avgDeaths(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => round(floatval($value), 2),
        );
    }

    public function avgAssists(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => round(floatval($value), 2),
        );
    }

    public function avgCs(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function avgDamageDealtToChampions(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function avgGold(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function avgDamageTaken(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function totalDoubleKills(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function totalTripleKills(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function totalQuadraKills(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function totalPentaKills(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => intval($value),
        );
    }

    public function scopeFilters(Builder $query, $filters): Builder
    {
        if (! empty($filters)) {
            if (Arr::get($filters, 'queue') != null || Arr::get($filters, 'date_start') != null || Arr::get($filters, 'date_end') != null) {
                $query = $query->whereHas('match', function (Builder $query) use ($filters) {
                    if (Arr::get($filters, 'queue') != null) {
                        $query->where('queue_id', $filters['queue']);
                    }
                    if (Arr::get($filters, 'date_start') != null) {
                        $query->where('match_creation', '>=', $filters['date_start']);
                    }
                    if (Arr::get($filters, 'date_end') != null) {
                        $query->where('match_creation', '<=', $filters['date_end']);
                    }
                });
            }
            if (Arr::get($filters, 'champion') != null) {
                $query->where('champion_id', $filters['champion']);
            }
        }

        return $query;
    }

    public static function getStats(Collection $matches)
    {
    }

    public function scopeChampionsCalc(Builder $query, $championIds)
    {
        return $query->select(
            'champion_id',
            DB::raw('count(*) as total'),
            DB::raw('sum(won) as wins'),
            DB::raw('avg(kills) as avg_kills'),
            DB::raw('avg(deaths) as avg_deaths'),
            DB::raw('avg(assists) as avg_assists'),
            DB::raw('avg(minions_killed) as avg_cs'),
            DB::raw('max(kills) as max_kills'),
            DB::raw('max(deaths) as max_deaths'),
            DB::raw('max(assists) as max_assists'),
            DB::raw('avg(JSON_EXTRACT(stats, "$.total_damage_dealt_to_champions")) as avg_damage_dealt_to_champions'),
            DB::raw('avg(JSON_EXTRACT(stats, "$.gold_earned")) as avg_gold'),
            DB::raw('avg(JSON_EXTRACT(stats, "$.total_damage_taken")) as avg_damage_taken'),
            DB::raw('sum(double_kills) as total_double_kills'),
            DB::raw('sum(triple_kills) as total_triple_kills'),
            DB::raw('sum(quadra_kills) as total_quadra_kills'),
            DB::raw('sum(penta_kills) as total_penta_kills'),
        )
            ->with('champion:id,name,img_url')
            ->whereIn('champion_id', $championIds)
            ->groupBy('champion_id')
            ->orderBy('total', 'desc');
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
