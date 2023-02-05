<?php

namespace App\Models;

use App\Data\item\ItemMythicStats;
use App\Data\item\ItemStats;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Item
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property array $tags
 * @property int $gold
 * @property \Spatie\LaravelData\Contracts\BaseData|null $stats
 * @property \Spatie\LaravelData\Contracts\BaseData|null|null $mythic_stats
 * @property string $colloq
 * @property string $img_url
 * @property string|null $type
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereColloq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereGold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereMythicStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereType($value)
 *
 * @mixin \Eloquent
 */
class Item extends Model
{
    public $timestamps = false;

    protected $fillable = ['name',
        'id',
        'description',
        'img_url',
        'description',
        'tags',
        'gold',
        'stats',
        'mythic_stats',
        'colloq',
        'type',
    ];

    public $casts = [
        'tags' => 'array',
        'stats' => ItemStats::class,
        'mythic_stats' => ItemMythicStats::class,
    ];

    public static function url(string $version, string $url): string
    {
        return "https://ddragon.leagueoflegends.com/cdn/{$version}/img/item/{$url}";
    }
}
