<?php

// lang/en/admin.php

return [
    'permissions' => [
        'title' => 'Permission Management',
        'description' => 'Review registered permissions, guards, and role assignments.',

        'actions' => [
            'edit_role_permissions' => 'Edit Role Management',
        ],

        'labels' => [
            'assigned_roles' => 'Assigned roles',
            'sort_order' => 'Sort order',
            'system_permission' => 'System permission',
        ],

        'filters' => [
            'title' => 'Filtering',
            'description' => 'Refine the list of permissions by name, guard, category, role assignment, and system status.',
            'search' => [
                'placeholder' => 'Search by permission name',
            ],
            'guards' => [
                'all' => 'All guards',
            ],
            'categories' => [
                'all' => 'All categories',
            ],
            'roles' => [
                'all' => 'All roles',
            ],
            'assignment' => [
                'label' => 'Assignment',
            ],
            'system' => [
                'label' => 'System',
            ],
        ],

        'overview' => [
            'title' => 'Overview',
            'description' => 'Summary of permissions, guards, and role assignments.',
            'total' => [
                'heading' => 'Total permissions',
                'text' => 'The total number of registered permissions.',
            ],
            'guards' => [
                'heading' => 'Guards',
                'text' => 'Distinct guards used by registered permissions.',
            ],
            'assigned' => [
                'heading' => 'Assigned permissions',
                'text' => 'Permissions assigned to at least one role.',
            ],
            'unassigned' => [
                'heading' => 'Unassigned permissions',
                'text' => 'Permissions not currently assigned to any role.',
            ],
        ],

        'table' => [
            'title' => 'Permission List',
            'description' => 'Review registered permissions, guards, categories, and role assignments.',
            'empty' => 'No permissions registered yet.',
            'columns' => [
                'sort' => 'Sort',
                'flags' => 'Flags',
                'assigned_roles' => 'Assigned roles',
            ],
        ],

        'modals' => [
            'edit' => [
                'title' => 'Edit Permission',
                'description' => 'Edit metadata for this permission. Name and guard are not changed here.',
                'metadata_section' => 'Permission Metadata',
                'details_section' => 'Edit Permission Details',
                'editable_scope' => 'Editable scope',
                'metadata_only' => 'Metadata only',
                'category_placeholder' => 'e.g. users, settings, system',
                'description_placeholder' => 'Describe what this permission allows.',
            ],
            'roles' => [
                'title' => 'Manage Role Permissions',
                'description' => 'Assign permissions to a selected role.',
                'edit_section' => 'Edit Role Permissions',
                'select_role' => 'Select role',
                'overview_for' => 'Role Permission Overview for :role',
                'current_permissions' => 'Current permissions',
                'selected_permissions' => 'Selected permissions',
                'changes' => 'Changes',
                'set_permissions_section' => 'Set Role Permissions',
            ],
        ],

        'messages' => [
            'no_changes' => [
                'heading' => 'No changes',
                'role_permissions_unchanged' => 'The permissions for :role have not changed.',
            ],
            'role_permissions_saved' => [
                'heading' => 'Role permissions saved',
                'text' => 'Permissions for :role have been updated.',
            ],
            'permission_saved' => [
                'heading' => 'Permission saved',
                'text' => 'Permission metadata for :permission has been updated.',
            ],
        ],
    ],
];
