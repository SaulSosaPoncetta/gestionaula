<?php

return [
    'url' => env('MIGESTION_HUB_URL'),
    'api_key' => env('MIGESTION_HUB_API_KEY'),
    'webhook_secret' => env('MIGESTION_HUB_WEBHOOK_SECRET'),
    'cache_horas' => env('MIGESTION_HUB_CACHE_HORAS', 6),
];