<?php
// config for Ezzaze/Airlabs
return [
    'api_key' => env('AIRLABS_API_KEY', ''),
    'version' => env('AIRLABS_API_VERSION', 'v9'),
    'cache' => [
        'lifetime' => env('AIRLABS_API_CACHE_LIFETIME', null)
    ],
];
