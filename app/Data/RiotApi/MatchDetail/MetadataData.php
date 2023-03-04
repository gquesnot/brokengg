<?php

namespace App\Data\RiotApi\MatchDetail;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
class MetadataData extends \Spatie\LaravelData\Data
{

    public function __construct(
        public string $data_version,
        public string $match_id,
        #[MapInputName('participants')]
        public array $participant_puuids
    )
    {
    }


}
