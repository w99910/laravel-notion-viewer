<?php

return [
    'API_KEY' => env('NOTION_API_KEY'),

    'API_VERSION' => env('NOTION_API_VERSION'),
    
    'cache' => [
        'enabled' => false,
        'time' => 60, // in seconds
    ]
];
