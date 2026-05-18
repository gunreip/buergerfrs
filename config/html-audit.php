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
                'icon' => 'building-2',
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
