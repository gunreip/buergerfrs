<?php

// lang/de/admin.php

return [
    'permissions' => [
        'title' => 'Berechtigungsverwaltung',
        'description' => 'Registrierte Berechtigungen, Guards und Rollenzuweisungen prüfen.',

        'actions' => [
            'edit_role_permissions' => 'Rollenberechtigungen bearbeiten',
        ],

        'labels' => [
            'assigned_roles' => 'Zugewiesene Rollen',
            'sort_order' => 'Sortierreihenfolge',
            'system_permission' => 'Systemberechtigung',
        ],

        'filters' => [
            'title' => 'Filter',
            'description' => 'Liste der Berechtigungen nach Name, Guard, Kategorie, Rollenzuweisung und Systemstatus eingrenzen.',
            'search' => [
                'placeholder' => 'Nach Berechtigungsname suchen',
            ],
            'guards' => [
                'all' => 'Alle Guards',
            ],
            'categories' => [
                'all' => 'Alle Kategorien',
            ],
            'roles' => [
                'all' => 'Alle Rollen',
            ],
            'assignment' => [
                'label' => 'Zuweisung',
            ],
            'system' => [
                'label' => 'System',
            ],
        ],

        'overview' => [
            'title' => 'Übersicht',
            'description' => 'Zusammenfassung der Berechtigungen, Guards und Rollenzuweisungen.',
            'total' => [
                'heading' => 'Berechtigungen gesamt',
                'text' => 'Gesamtzahl der registrierten Berechtigungen.',
            ],
            'guards' => [
                'heading' => 'Guards',
                'text' => 'Unterschiedliche Guards der registrierten Berechtigungen.',
            ],
            'assigned' => [
                'heading' => 'Zugewiesene Berechtigungen',
                'text' => 'Berechtigungen, die mindestens einer Rolle zugewiesen sind.',
            ],
            'unassigned' => [
                'heading' => 'Nicht zugewiesene Berechtigungen',
                'text' => 'Berechtigungen, die aktuell keiner Rolle zugewiesen sind.',
            ],
        ],

        'table' => [
            'title' => 'Berechtigungsliste',
            'description' => 'Registrierte Berechtigungen, Guards, Kategorien und Rollenzuweisungen prüfen.',
            'empty' => 'Es sind noch keine Berechtigungen registriert.',
            'columns' => [
                'sort' => 'Sortierung',
                'flags' => 'Flags',
                'assigned_roles' => 'Zugewiesene Rollen',
            ],
        ],

        'modals' => [
            'edit' => [
                'title' => 'Berechtigung bearbeiten',
                'description' => 'Metadaten dieser Berechtigung bearbeiten. Name und Guard werden hier nicht geändert.',
                'metadata_section' => 'Berechtigungsmetadaten',
                'details_section' => 'Berechtigungsdetails bearbeiten',
                'editable_scope' => 'Bearbeitbarer Bereich',
                'metadata_only' => 'Nur Metadaten',
                'category_placeholder' => 'z. B. users, settings, system',
                'description_placeholder' => 'Beschreibe, was diese Berechtigung erlaubt.',
            ],
            'roles' => [
                'title' => 'Rollenberechtigungen verwalten',
                'description' => 'Berechtigungen einer ausgewählten Rolle zuweisen.',
                'edit_section' => 'Rollenberechtigungen bearbeiten',
                'select_role' => 'Rolle auswählen',
                'overview_for' => 'Berechtigungsübersicht für Rolle :role',
                'current_permissions' => 'Aktuelle Berechtigungen',
                'selected_permissions' => 'Ausgewählte Berechtigungen',
                'changes' => 'Änderungen',
                'set_permissions_section' => 'Rollenberechtigungen setzen',
            ],
        ],

        'messages' => [
            'no_changes' => [
                'heading' => 'Keine Änderungen',
                'role_permissions_unchanged' => 'Die Berechtigungen für :role wurden nicht geändert.',
            ],
            'role_permissions_saved' => [
                'heading' => 'Rollenberechtigungen gespeichert',
                'text' => 'Die Berechtigungen für :role wurden aktualisiert.',
            ],
            'permission_saved' => [
                'heading' => 'Berechtigung gespeichert',
                'text' => 'Die Metadaten für :permission wurden aktualisiert.',
            ],
        ],
    ],
];
