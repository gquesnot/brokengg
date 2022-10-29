<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Queue
 *
 * @property int $id
 * @property string $map
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Queue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue query()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperQueue
 */
class Queue extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'map',
        'description', ];
}
