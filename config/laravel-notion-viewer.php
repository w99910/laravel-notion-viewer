<?php

return [
    'API_KEY' => env('NOTION_API_KEY'),

    'API_VERSION' => env('NOTION_API_VERSION'),

    'cache' => [
        'enabled' => false, // If you want to cache the response, set this to true
        'time' => 60, // in seconds
    ]
];
