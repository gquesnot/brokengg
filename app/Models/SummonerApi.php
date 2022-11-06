<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SummonerApi
 *
 * @property int $id
 * @property string|null $api_summoner_id
 * @property string|null $api_account_id
 * @property string|null $puuid
 * @property int $summoner_id
 * @property int $account_id
 * @property-read \App\Models\ApiAccount|null $apiAccount
 * @property-read \App\Models\Summoner|null $summoner
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi query()
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi whereApiAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi whereApiSummonerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi wherePuuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SummonerApi whereSummonerId($value)
 * @mixin \Eloquent
 */
class SummonerApi extends Model
{

    public $timestamps = false;
    public $fillable = [
        "summoner_id",
        "account_id",
        "puuid",
        "api_summoner_id",
        'api_account_id',
    ];


    public function summoner()
    {
        return $this->belongsTo(Summoner::class);
    }

    public function apiAccount()
    {
        return $this->belongsTo(ApiAccount::class, 'account_id');
    }
}