<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Primary Language Scope Default
    |--------------------------------------------------------------------------
    |
    | The scope that should be checked by default in the segmented control.
    | Allowed values: any configured scope key, or "all".
    |
    */
    'primary_language_scope_default' => 'eu-top',

    /*
    |--------------------------------------------------------------------------
    | Primary Language Scopes
    |--------------------------------------------------------------------------
    |
    | Defines the segmented filter sets shown before the primary language
    | selector in App Settings. The values are normalized ISO language codes.
    |
    */
    'primary_language_scopes' => [
        'top10' => [
            'zh',
            'en',
            'hi',
            'es',
            'ar',
            'bn',
            'fr',
            'ru',
            'pt',
            'ur',
        ],

        'top20' => [
            'zh',
            'en',
            'hi',
            'es',
            'ar',
            'bn',
            'fr',
            'ru',
            'pt',
            'ur',
            'id',
            'de',
            'ja',
            'sw',
            'tr',
            'ta',
            'vi',
            'ko',
            'te',
            'fa',
        ],

        'eu-top' => [
            'en',
            'de',
            'fr',
            'it',
            'es',
            'pl',
            'nl',
            'ro',
            'sv',
            'cs',
        ],

        // 'dach' => [
        //     'de',
        //     'fr',
        //     'it',
        // ],

        // 'mena' => [
        //     'ar',
        //     'fa',
        //     'tr',
        //     'ku',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Primary Language Scope Labels
    |--------------------------------------------------------------------------
    |
    | Optional custom labels for segmented primary-language scope buttons.
    | Keys must match the scope keys above. Missing labels are auto-generated.
    |
    */
    'primary_language_scope_labels' => [
        'top10' => 'Top 10',
        'top20' => 'Top 20',
        'eu-top' => 'EU Top',
        // 'dach' => 'DACH',
        // 'mena' => 'MENA',
        'all' => 'All',
    ],

    /*
    |--------------------------------------------------------------------------
    | Primary Language Scope Icons
    |--------------------------------------------------------------------------
    |
    | Optional custom Flux icon names for segmented scope buttons.
    | Keys must match the scope keys above. Missing icons fall back to
    | list-ordered, and for "all" to arrow-down-a-z.
    |
    */
    'primary_language_scope_icons' => [
        'top10' => 'list-ordered',
        'top20' => 'list-ordered',
        'eu-top' => 'earth',
        // 'dach' => 'mountain',
        // 'mena' => 'globe-2',
        'all' => 'arrow-down-a-z',
    ],
];
