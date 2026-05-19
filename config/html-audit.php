<?php

// config/html-audit.php

return [
    'component_tag_scan' => [
        'paths' => [
            resource_path('views'),
        ],

        'exclude_path_fragments' => [
            '/xxx/',
            '/yyy/',
            '/zzz/',
        ],

        'exclude_file_name_fragments' => [
            'xxx',
            'yyy',
            'zzz',
        ],

        'include_prefixes' => [
            'flux:',
            'x-',
            'livewire:',
        ],

        'exclude_prefixes' => [
            'x-slot',
            'x-slot:',
        ],

        'preview_limit' => 20,
    ],

    'view_html_used' => [
        'scan_paths' => [
            resource_path('views'),
        ],

        'native_reference_path' => storage_path('audits/html/native-html-tags.json'),

        'output_path' => storage_path('audits/html/view-html-used.json'),
        'preview_path' => storage_path('audits/html/view-html-used-preview.json'),

        'component_paths' => [
            'custom' => [
                [
                    'path' => resource_path('views/components/ui'),
                    'prefix' => 'x-ui.',
                ],
            ],

            'flux' => [
                [
                    'path' => resource_path('views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => resource_path('views/vendor/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux/stubs/resources/views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux/resources/views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux/resources/views/components'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux-pro/stubs/resources/views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux-pro/resources/views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux-pro/resources/views/components'),
                    'prefix' => 'flux:',
                ],
            ],
        ],

        'component_tag_prefixes' => [
            'custom' => [
                'x-ui.',
            ],

            'flux' => [
                'flux:',
            ],
        ],

        'exclude_path_fragments' => [
            '/xxx/',
            '/yyy/',
            '/zzz/',
        ],

        'exclude_file_name_fragments' => [
            'xxx',
            'yyy',
            'zzz',
        ],

        'preview_limit' => 20,
    ],

    'view_html_used' => [
        'scan_paths' => [
            resource_path('views'),
        ],

        'native_reference_path' => storage_path('audits/html/native-html-tags.json'),

        'output_path' => storage_path('audits/html/view-html-used.json'),
        'preview_path' => storage_path('audits/html/view-html-used-preview.json'),

        'component_paths' => [
            'custom' => [
                [
                    'path' => resource_path('views/components/ui'),
                    'prefix' => 'x-ui.',
                ],
            ],

            'flux' => [
                [
                    'path' => resource_path('views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => resource_path('views/vendor/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux/stubs/resources/views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux/resources/views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux/resources/views/components'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux-pro/stubs/resources/views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux-pro/resources/views/flux'),
                    'prefix' => 'flux:',
                ],
                [
                    'path' => base_path('vendor/livewire/flux-pro/resources/views/components'),
                    'prefix' => 'flux:',
                ],
            ],
        ],

        'component_tag_prefixes' => [
            'custom' => [
                'x-ui.',
            ],

            'flux' => [
                'flux:',
            ],
        ],

        'exclude_path_fragments' => [
            '/xxx/',
            '/yyy/',
            '/zzz/',
        ],

        'exclude_file_name_fragments' => [
            'xxx',
            'yyy',
            'zzz',
        ],

        'preview_limit' => 20,
    ],

    'table_legend' => [
        'status' => [
            'open' => [
                'label' => 'Open',
                'description' => 'The finding is present in the latest audit run.',
                'icon' => 'bug',
                'symbol' => '!',
                'color' => 'red',
            ],
            'changed' => [
                'label' => 'Changed',
                'description' => 'The finding changed or moved and was superseded by a newer finding.',
                'icon' => 'queue-list',
                'symbol' => '↻',
                'color' => 'amber',
            ],
            'resolved' => [
                'label' => 'Resolved',
                'description' => 'The finding is no longer present in the latest audit run.',
                'icon' => 'check',
                'symbol' => '✓',
                'color' => 'green',
            ],
            'ignored' => [
                'label' => 'Ignored',
                'description' => 'The finding is intentionally ignored.',
                'icon' => 'shield-x',
                'symbol' => '⊘',
                'color' => 'zinc',
            ],
        ],

        'section' => [
            'native_html' => [
                'label' => 'Native HTML',
                'description' => 'Problems found in native HTML tag structure.',
                'icon' => 'code-xml',
                'symbol' => '</>',
                'color' => 'amber',
            ],
            'custom_components' => [
                'label' => 'Custom components',
                'description' => 'Problems found in Flux, Livewire or custom Blade component structure.',
                'icon' => 'grid-3x3',
                'symbol' => '▦',
                'color' => 'zinc',
            ],
        ],

        'type' => [
            'mismatched' => [
                'label' => 'Mismatched',
                'description' => 'The closing tag does not match the currently open tag.',
                'icon' => 'tag',
                'symbol' => '◇',
                'color' => 'amber',
            ],
            'unclosed' => [
                'label' => 'Unclosed',
                'description' => 'An opening tag was found without a matching closing tag.',
                'icon' => 'link',
                'symbol' => '⛓',
                'color' => 'red',
            ],
        ],
    ],
];
