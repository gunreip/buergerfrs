<?php

// config/buergerfrs-icons.php

return [

    /*
    |--------------------------------------------------------------------------
    | Role/User Management Icons
    |--------------------------------------------------------------------------
    |
    | Icons listed here are allowed for role/user-management display contexts.
    | The key is the application-facing icon name stored in settings.
    | The component value must match a deployed Flux icon component.
    |
    | If an icon is configured but not deployed/available, rendering must fall
    | back to the configured fallback icon.
    |
    */

    'fallback' => [
        'name' => 'file-x',
        'label' => 'Missing icon',
        'view' => 'flux.icon.file-x',
    ],

    'categories' => [
        'role_user_management' => [
            'label' => 'Role/User Management',

            'badge' => [
                'colors' => [
                    'zinc' => 'Zinc',
                    'red' => 'Red',
                    'orange' => 'Orange',
                    'amber' => 'Amber',
                    'yellow' => 'Yellow',
                    'lime' => 'Lime',
                    'green' => 'Green',
                    'emerald' => 'Emerald',
                    'teal' => 'Teal',
                    'cyan' => 'Cyan',
                    'sky' => 'Sky',
                    'blue' => 'Blue',
                    'indigo' => 'Indigo',
                    'violet' => 'Violet',
                    'purple' => 'Purple',
                    'fuchsia' => 'Fuchsia',
                    'pink' => 'Pink',
                    'rose' => 'Rose',
                ],

                'variants' => [
                    'solid' => 'Solid',
                    'subtle' => 'Subtle',
                    'outline' => 'Outline',
                    'pill' => 'Pill',
                ],
            ],

            'icons' => [
                'tag' => [
                    'label' => 'Tag',
                    'view' => 'flux.icon.tag',
                ],

                'shield-check' => [
                    'label' => 'Shield Check',
                    'view' => 'flux.icon.shield-check',
                ],

                'crown' => [
                    'label' => 'Crown',
                    'view' => 'flux.icon.crown',
                ],

                'user' => [
                    'label' => 'User',
                    'view' => 'flux.icon.user',
                ],

                'users' => [
                    'label' => 'Users',
                    'view' => 'flux.icon.users',
                ],

                'key-round' => [
                    'label' => 'Key Round',
                    'view' => 'flux.icon.user-round-key',
                ],

                'lock-keyhole' => [
                    'label' => 'Lock Keyhole',
                    'view' => 'flux.icon.lock-keyhole',
                ],

                'badge-check' => [
                    'label' => 'Badge Check',
                    'view' => 'flux.icon.badge-check',
                ],

                'headset' => [
                    'label' => 'Headset',
                    'view' => 'flux.icon.headset',
                ],
            ],
        ],
    ],
];
