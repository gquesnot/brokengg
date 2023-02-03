<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Queue
 *
 * @property int $id
 * @property string $map
 * @property string $description
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Queue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue query()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereMap($value)
 *
 * @mixin \Eloquent
 */
class Queue extends Model
{
    public $timestamps = false;

    protected $fillable = ['id', 'map',
        'description', ];

    public function name()
    {
        return str_replace('Pick', '', str_replace(' games', '', $this->description));
    }
}
