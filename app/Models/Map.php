<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Map
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Map newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map query()
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Map extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
    ];
}
