<?php

namespace App\Traits;

trait DataMappingTrait
{
    public static function mapping(array $datas): self
    {
        $result = [];
        $mapping = self::getMapping();
        foreach ($mapping as $key => $map) {
            if (array_key_exists($key, $datas)) {
                $result[$map] = $datas[$key];
            }
        }
        //dd($result);
        return self::class::from($result);
    }
}
