<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Version
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Version newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Version newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Version query()
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperVersion
 */
class Version extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
