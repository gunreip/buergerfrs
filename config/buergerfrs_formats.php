<?php

// config/buergerfrs_formats.php

return [
    'date_time' => [
        'timezone' => env('APP_DISPLAY_TIMEZONE', env('APP_TIMEZONE', 'Europe/Berlin')),

        'formats' => [
            'date_time' => 'ddd, LL, LT',
            'date' => 'ddd, LL',
            'time' => 'LT',
        ],
    ],
];
