<?php

namespace App\Models;

use App\Enums\Rank;
use App\Enums\RankedType;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SummonerLeague
 *
 * @property int $id
 * @property RankedType $type
 * @property int $summoner_id
 * @property Rank $rank
 * @property int $rank_number
 * @property \App\Enums\Tier $tier
 * @property-read \App\Models\Summoner|null $summoner
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague query()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereRankNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereSummonerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerLeague whereType($value)
 *
 * @mixin \Eloquent
 */
class SummonerLeague extends Model
{
    public $timestamps = false;

    public $fillable = [
        'type',
        'summoner_id',
        'rank',
        'tier',
        'rank_number',
    ];

    public $casts = [
        'type' => \App\Enums\RankedType::class,
        'tier' => \App\Enums\Tier::class,
        'rank' => Rank::class,
    ];

    public function getRankNumber()
    {
        if ($this->tier->number() >= 25) { // master
            return $this->tier->number();
        }

        return $this->tier->number() + $this->rank->number($this->tier);
    }

    public function summoner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Summoner::class);
    }
}
