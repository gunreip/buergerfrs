<?php

return [
    'version' => [
        'fallback' => '0.7.0',
        'package_path' => 'packages/gunreip/laravel-translation-workbench',
        'tag_prefixes' => [
            'translation-workbench/v',
            'v',
        ],
    ],

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

    'timeline_presentation' => [
        'events' => [
            'finding_discovered' => [
                'primary' => ['literal_text', 'literal_text_suggested', 'found_translation_key', 'suggested_key'],
                'secondary' => ['kind', 'function_name', 'namespace', 'group', 'path_key', 'scope', 'entry_type', 'candidate_type', 'status'],
            ],
            'key_candidate_discovered' => [
                'primary' => ['suggested_key', 'key_type'],
                'secondary' => ['namespace', 'group', 'path_key', 'scope', 'status', 'review_status', 'fingerprint'],
            ],
            'key_finding_relation_created' => [
                'primary' => ['key_id', 'finding_id', 'relation_type'],
                'secondary' => ['status'],
            ],
            'key_finding_relation_obsoleted' => [
                'primary' => ['status'],
                'secondary' => ['obsolete_reason', 'meta'],
            ],
            'translation_key_bulk_equalized' => [
                'primary' => ['translation_key', 'namespace', 'group'],
                'secondary' => ['path_key', 'scope', 'key_type', 'review_status', 'is_ui_key', 'is_dynamic_key', 'is_dynamic_multi'],
            ],
            'translation_key_changed' => [
                'primary' => ['translation_key', 'namespace', 'group'],
                'secondary' => ['path_key', 'scope', 'review_status'],
            ],
            'translation_key_updated' => [
                'primary' => ['translation_key', 'namespace', 'group'],
                'secondary' => ['path_key', 'scope', 'key_type', 'review_status', 'is_ui_key'],
            ],
            'translation_value_saved' => [
                'primary' => ['value', 'native_label', 'locale'],
                'secondary' => ['status', 'locale_role'],
            ],
            'translation_values_saved' => [
                'primary' => ['values'],
                'secondary' => [],
            ],
            'lang_file_value_exported' => [
                'primary' => ['value'],
                'secondary' => ['locale', 'namespace', 'lang_key', 'translation_key', 'state', 'path'],
            ],
            'lang_file_value_pruned' => [
                'primary' => ['value'],
                'secondary' => ['locale', 'namespace', 'lang_key', 'translation_key', 'reason', 'path'],
            ],
            'code_update_applied' => [
                'primary' => ['raw_expression', 'new_expression'],
                'secondary' => ['source_path', 'source_line', 'translation_key', 'reason', 'replacement_scope', 'occurrences'],
            ],
            'dynamic_source_classification_changed' => [
                'primary' => ['classification', 'cardinality', 'status', 'dynamic_data_state'],
                'secondary' => ['source_type', 'source_reference'],
            ],
        ],
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
