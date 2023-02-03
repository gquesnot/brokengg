<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Map
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Map newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map query()
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereName($value)
 *
 * @mixin \Eloquent
 */
class Map extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'description',
    ];
}
