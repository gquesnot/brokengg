<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tier
 *
 * @property int $id
 * @property string $name
 * @property int $position
 * @property string|null $img_url
 * @method static \Illuminate\Database\Eloquent\Builder|Tier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier wherePosition($value)
 * @mixin \Eloquent
 */
class Tier extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'img_url',
        'position',
    ];
}
