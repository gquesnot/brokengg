<?php

namespace App\Data;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Transformers\Transformer;

class IntCast implements Cast
{

    public function cast(DataProperty $property, mixed $value, array $context): mixed
    {
        return (int) $value;
    }
}
