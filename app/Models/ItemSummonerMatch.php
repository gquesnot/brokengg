<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ItemSummonerMatch
 *
 * @property int $item_id
 * @property int $summoner_match_id
 * @property int $position
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSummonerMatch whereSummonerMatchId($value)
 *
 * @mixin \Eloquent
 */
class ItemSummonerMatch extends Model
{
    public $table = 'item_summoner_match';

    public $timestamps = false;

    protected $fillable = ['item_id', 'summoner_match_id', 'position'];
}
