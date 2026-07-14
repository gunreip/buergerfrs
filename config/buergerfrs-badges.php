<?php

// config/buergerfrs-badges.php

// Config file defining badge styles for different contexts in the Bürgerfrs application. This file is used to centralize the styling of badges based on their context and value, allowing for consistent visual representation across the application.
// <x-ui.badge.context ... /> component will use this configuration to determine the appropriate color and variant for badges based on their context and value.

return [
    'fallback' => [
        'color' => 'zinc',
        'variant' => 'subtle',
    ],

    'contexts' => [
        'translation.key.status' => [
            'ok' => [
                'color' => 'green',
                'variant' => 'subtle',
            ],
            'missing' => [
                'color' => 'amber',
                'variant' => 'subtle',
            ],
            'native' => [
                'color' => 'sky',
                'variant' => 'subtle',
            ],
            'dynamic' => [
                'color' => 'purple',
                'variant' => 'subtle',
            ],
            'dynamic-multi' => [
                'color' => 'orange',
                'variant' => 'subtle',
            ],
        ],

        'translation.value.status' => [
            'ok' => [
                'color' => 'green',
                'variant' => 'subtle',
            ],
            'missing' => [
                'color' => 'amber',
                'variant' => 'subtle',
            ],
            'stale' => [
                'color' => 'red',
                'variant' => 'subtle',
            ],
        ],

        'translation.key.classification' => [
            'key' => [
                'color' => 'blue',
                'variant' => 'subtle',
            ],
            'native' => [
                'color' => 'sky',
                'variant' => 'subtle',
            ],
            'dynamic' => [
                'color' => 'purple',
                'variant' => 'subtle',
            ],
            'dynamic-multi' => [
                'color' => 'orange',
                'variant' => 'subtle',
            ],
        ],

        'translation.key.suggestion' => [
            'missing_key' => [
                'color' => 'amber',
                'variant' => 'subtle',
            ],
            'matches_suggested_key' => [
                'color' => 'green',
                'variant' => 'subtle',
            ],
            'differs_from_suggested_key' => [
                'color' => 'red',
                'variant' => 'subtle',
            ],
            'no_suggestion' => [
                'color' => 'zinc',
                'variant' => 'subtle',
            ],
        ],
    ],
];
