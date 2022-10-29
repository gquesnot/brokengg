<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Champion
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $img_url
 * @property string $champion_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SummonerMatch[] $matches
 * @property-read int|null $matches_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Champion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Champion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Champion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereChampionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Champion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Champion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'champion_id',
        'id',
        'title',
        'img_url',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(SummonerMatch::class, 'champion_id', 'id');
    }
}
