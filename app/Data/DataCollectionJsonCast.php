<?php

namespace App\Data;

use App\Casts\CastableCollectionJsonData;
use App\Traits\WireableData;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Spatie\LaravelData\Data;

class DataCollectionJsonCast extends Data implements Castable
{
    use WireableData;

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
        return new CastableCollectionJsonData(static::class);
    }
}
