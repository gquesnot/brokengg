<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\ApiAccount
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property bool $actif
 * @property string|null $api_key
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerApi[] $summonerApis
 * @property-read int|null $summoner_apis_count
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount whereActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiAccount whereUsername($value)
 * @mixin \Eloquent
 */
class ApiAccount extends Model
{

    public $fillable = [
        "username",
        "password",
        "api_key",
        'actif'
    ];

    public $casts =['actif' => 'boolean'];

    public  $timestamps = false;

    public ?Carbon $restart_at;
    public bool $limit_reached;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->restart_at = null;
        $this->limit_reached = false;
    }



    public function summonerApis()
    {
        return $this->hasMany(SummonerApi::class, 'account_id');
    }

}