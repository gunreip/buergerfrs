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

    'runtime_collector' => [
        'enabled' => env('TRANSLATION_WORKBENCH_RUNTIME_COLLECTOR_ENABLED', true),
    ],

    'editor' => [
        'vscode_wsl_distro' => env('TRANSLATION_WORKBENCH_VSCODE_WSL_DISTRO', env('WSL_DISTRO_NAME', 'Ubuntu-24.04')),
    ],

    'ui_state' => [
        'setting_key' => 'ui.pages.translation_workbench.entries',
        'defaults_file' => 'packages/gunreip/laravel-translation-workbench/resources/ui-state/entries-defaults.json',
        'export_file' => 'app/translation-workbench/ui-state/entries.json',
        'defaults' => [
            'findingSearch' => '',
            'findingStatus' => 'all',
            'findingKind' => 'all',
            'findingCandidateType' => 'all',
            'findingNamespace' => 'all',
            'findingGroup' => 'all',
            'findingKeyRelation' => 'all',
            'findingSourceValue' => 'all',
            'perPage' => 25,
            'findingSortField' => 'last_seen',
            'findingSortDirection' => 'desc',
            'showOverviewTabs' => true,
            'search' => '',
            'kind' => '',
            'status' => '',
            'dynamicFilter' => '',
            'dynamicOptionFilter' => '',
            'optionDiscoveryFilter' => '',
            'workflowFilter' => '',
            'sortField' => 'last_seen_at',
            'sortDirection' => 'desc',
            'showDynamicTable' => true,
            'showEntriesTable' => true,
        ],
    ],
];
