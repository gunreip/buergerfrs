<?php

return [
    'admin' => [
        'permissions' => [
            'actions' => [
                'edit_role_permissions' => 'Edit Role Management',
            ],
            'description' => 'Review registered permissions, guards, and role assignments.',
            'filters' => [
                'assignment' => [
                    'label' => 'Assignment',
                ],
                'categories' => [
                    'all' => 'All categories',
                ],
                'description' => 'Refine the list of permissions by name, guard, category, role assignment, and system status.',
                'guards' => [
                    'all' => 'All guards',
                ],
                'roles' => [
                    'all' => 'All roles',
                ],
                'search' => [
                    'placeholder' => 'Search by permission name',
                ],
                'system' => [
                    'label' => 'System',
                ],
                'title' => 'Filtering',
            ],
            'labels' => [
                'assigned_roles' => 'Assigned roles',
                'sort_order' => 'Sort order',
                'system_permission' => 'System permission',
            ],
            'messages' => [
                'no_changes' => [
                    'heading' => 'No changes',
                    'role_permissions_unchanged' => 'The permissions for :role have not changed.',
                ],
                'permission_saved' => [
                    'heading' => 'Permission saved',
                    'text' => 'Permission metadata for :permission has been updated.',
                ],
                'role_permissions_saved' => [
                    'heading' => 'Role permissions saved',
                    'text' => 'Permissions for :role have been updated.',
                ],
            ],
            'modals' => [
                'edit' => [
                    'category_placeholder' => 'e.g. users, settings, system',
                    'description' => 'Edit metadata for this permission. Name and guard are not changed here.',
                    'description_placeholder' => 'Describe what this permission allows.',
                    'details_section' => 'Edit Permission Details',
                    'editable_scope' => 'Editable scope',
                    'metadata_only' => 'Metadata only',
                    'metadata_section' => 'Permission Metadata',
                    'title' => 'Edit Permission',
                ],
                'roles' => [
                    'changes' => 'Changes',
                    'current_permissions' => 'Current permissions',
                    'description' => 'Assign permissions to a selected role.',
                    'edit_section' => 'Edit Role Permissions',
                    'overview_for' => 'Role Permission Overview for :role',
                    'select_role' => 'Select role',
                    'selected_permissions' => 'Selected permissions',
                    'set_permissions_section' => 'Set Role Permissions',
                    'title' => 'Manage Role Permissions',
                ],
            ],
            'overview' => [
                'assigned' => [
                    'heading' => 'Assigned permissions',
                    'text' => 'Permissions assigned to at least one role.',
                ],
                'description' => 'Summary of permissions, guards, and role assignments.',
                'guards' => [
                    'heading' => 'Guards',
                    'text' => 'Distinct guards used by registered permissions.',
                ],
                'title' => 'Overview',
                'total' => [
                    'heading' => 'Total permissions',
                    'text' => 'The total number of registered permissions.',
                ],
                'unassigned' => [
                    'heading' => 'Unassigned permissions',
                    'text' => 'Permissions not currently assigned to any role.',
                ],
            ],
            'table' => [
                'columns' => [
                    'assigned_roles' => 'Assigned roles',
                    'flags' => 'Flags',
                    'sort' => 'Sort',
                ],
                'description' => 'Review registered permissions, guards, categories, and role assignments.',
                'empty' => 'No permissions registered yet.',
                'title' => 'Permission List',
            ],
            'title' => 'Permission Management',
        ],
    ],
];
