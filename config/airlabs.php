<?php
// config for Ezzaze/Airlabs
return [
    'api_key' => env('AIRLABS_API_KEY', ''),
    'version' => env('AIRLABS_API_VERSION', 'v9'),
    'cache' => [
        'enabled' => env('AIRLABS_API_CACHE_ENABLED', false),
        'lifetime' => env('AIRLABS_API_CACHE_LIFETIME', null)
    ],
];
