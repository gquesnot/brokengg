<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Mode
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Mode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereName($value)
 * @mixin \Eloquent
 */
class Mode extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'description'];
}
