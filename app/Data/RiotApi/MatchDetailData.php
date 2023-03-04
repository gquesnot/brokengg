<?php

namespace App\Data\RiotApi;

use App\Data\RiotApi\MatchDetail\InfoData;
use App\Data\RiotApi\MatchDetail\MetadataData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class MatchDetailData extends \Spatie\LaravelData\Data
{

    public function __construct(
        public MetadataData $metadata,
        public InfoData $info
    )
    {
    }

    static function fromResponse(\Saloon\Contracts\Response $response): static
    {
        return self::withoutMagicalCreationFrom($response->json());
    }
}
