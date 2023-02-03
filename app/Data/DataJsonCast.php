<?php

namespace App\Data;

use App\Casts\CastableJsonData;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Spatie\LaravelData\Data;

class DataJsonCast extends Data implements Castable
{
    use \Spatie\LaravelData\Concerns\WireableData;

    public function toArray(bool $get_all = false): array
    {
//         $array = parent::toArray();
//         if (!$get_all) {
//              foreach ($array as $key => $value) {
//                if ($value == 0 || $value == 0.0 || $value == "") {
//                     unset($array[$key]);
//                }
//              }
//         }
        return parent::toArray();
    }

     public static function castUsing(array $arguments)
     {
         return new CastableJsonData(static::class);
     }
}
