<?php

return [
    'paths' => [
        'app',
        'routes',
        'resources/views',
        'packages/gunreip/laravel-translation-workbench/resources/views',
    ],

    'file_patterns' => [
        '*.php',
        '*.blade.php',
    ],

    'ignored_filename_contains' => [
        'xxx',
        'yyy',
        'zzz',
    ],

    'translation_functions' => [
        '__',
        'trans',
        '@lang',
        'Lang::get',
    ],

    'source_locale' => 'en',

    'editor' => [
        'vscode_wsl_distro' => env('TRANSLATION_WORKBENCH_VSCODE_WSL_DISTRO', env('WSL_DISTRO_NAME', 'Ubuntu-24.04')),
    ],

    'ui_state' => [
        'setting_key' => 'ui.pages.translation_workbench.entries',
        'defaults' => [
            'search' => '',
            'kind' => '',
            'status' => '',
            'dynamicFilter' => '',
            'dynamicOptionFilter' => '',
            'optionDiscoveryFilter' => '',
            'workflowFilter' => '',
            'perPage' => 50,
            'sortField' => 'last_seen_at',
            'sortDirection' => 'desc',
            'showDynamicTable' => true,
            'showEntriesTable' => true,
        ],
    ],
];
