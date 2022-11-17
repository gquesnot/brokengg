<?php

return [
    'api_key' => env('RIOT_API_KEY', ''),
    'max_match_count' => env('RIOT_MAX_MATCHES_COUNT', 0),
    'min_match_date' => env('RIOT_MIN_MATCH_DATE', 0),
    'lol_path' => env('RIOT_LOL_PATH', ''),
];
