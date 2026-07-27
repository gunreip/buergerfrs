<?php

return [
    'paths' => [
        'app',
        'routes',
        'resources/views',
        'packages/gunreip/laravel-translation-workbench/resources/views',
    ],

    'exclude_paths' => [
        'packages/gunreip/laravel-translation-workbench/resources/views/livewire/old',
        'app/Console/Commands',
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
            'findingSearchExact' => false,
            'findingSearchCaseSensitive' => false,
            'findingStatus' => 'all',
            'findingKind' => 'all',
            'findingCandidateType' => 'all',
            'findingNamespace' => 'all',
            'findingGroup' => 'all',
            'findingKeyRelation' => 'all',
            'findingLiteralState' => 'all',
            'perPage' => 25,
            'findingSortField' => 'last_seen',
            'findingSortDirection' => 'desc',
            'showOverviewTabs' => true,
            'showObsoleteFindings' => false,
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

    'raw_data_ui_state' => [
        'setting_key' => 'ui.pages.translation_workbench.raw_data',
        'defaults_file' => 'packages/gunreip/laravel-translation-workbench/resources/ui-state/raw-data-defaults.json',
        'export_file' => 'translation-workbench/ui-state/raw-data.json',
        'defaults' => [
            'activeTable' => 'translation_workbench_source_files',
            'perPage' => 50,
            'sortField' => 'id',
            'sortDirection' => 'desc',
        ],
    ],
];
