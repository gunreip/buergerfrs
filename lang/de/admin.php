<?php

return [
    'permissions' => [
        'actions' => [
            'edit_role_permissions' => 'Rollenberechtigungen bearbeiten',
        ],
        'description' => 'Registrierte Berechtigungen, Guards und Rollenzuweisungen prüfen.',

        'filters' => [
            'assignment' => [
                'label' => 'Zuweisung',
            ],

            'categories' => [
                'all' => 'Alle Kategorien',
            ],
            'description' => 'Liste der Berechtigungen nach Name, Guard, Kategorie, Rollenzuweisung und Systemstatus eingrenzen.',

            'guards' => [
                'all' => 'Alle Guards',
            ],

            'roles' => [
                'all' => 'Alle Rollen',
            ],

            'search' => [
                'placeholder' => 'Nach Berechtigungsname suchen',
            ],

            'system' => [
                'label' => 'System',
            ],
            'title' => 'Filter',
        ],

        'labels' => [
            'assigned_roles' => 'Zugewiesene Rollen',
            'sort_order' => 'Sortierreihenfolge',
            'system_permission' => 'Systemberechtigung',
        ],

        'messages' => [
            'no_changes' => [
                'heading' => 'Keine Änderungen',
                'role_permissions_unchanged' => 'Die Berechtigungen für :role wurden nicht geändert.',
            ],

            'permission_saved' => [
                'heading' => 'Berechtigung gespeichert',
                'text' => 'Die Metadaten für :permission wurden aktualisiert.',
            ],

            'role_permissions_saved' => [
                'heading' => 'Rollenberechtigungen gespeichert',
                'text' => 'Die Berechtigungen für :role wurden aktualisiert.',
            ],
        ],

        'modals' => [
            'edit' => [
                'category_placeholder' => 'z. B. users, settings, system',
                'description' => 'Metadaten dieser Berechtigung bearbeiten. Name und Guard werden hier nicht geändert.',
                'description_placeholder' => 'Beschreibe, was diese Berechtigung erlaubt.',
                'details_section' => 'Berechtigungsdetails bearbeiten',
                'editable_scope' => 'Bearbeitbarer Bereich',
                'metadata_only' => 'Nur Metadaten',
                'metadata_section' => 'Berechtigungsmetadaten',
                'title' => 'Berechtigung bearbeiten',
            ],

            'roles' => [
                'changes' => 'Änderungen',
                'current_permissions' => 'Aktuelle Berechtigungen',
                'description' => 'Berechtigungen einer ausgewählten Rolle zuweisen.',
                'edit_section' => 'Rollenberechtigungen bearbeiten',
                'overview_for' => 'Berechtigungsübersicht für Rolle :role',
                'select_role' => 'Rolle auswählen',
                'selected_permissions' => 'Ausgewählte Berechtigungen',
                'set_permissions_section' => 'Rollenberechtigungen setzen',
                'title' => 'Rollenberechtigungen verwalten',
            ],
        ],

        'overview' => [
            'assigned' => [
                'heading' => 'Zugewiesene Berechtigungen',
                'text' => 'Berechtigungen, die mindestens einer Rolle zugewiesen sind.',
            ],
            'description' => 'Zusammenfassung der Berechtigungen, Guards und Rollenzuweisungen.',

            'guards' => [
                'heading' => 'Guards',
                'text' => 'Unterschiedliche Guards der registrierten Berechtigungen.',
            ],
            'title' => 'Übersicht',

            'total' => [
                'heading' => 'Berechtigungen gesamt',
                'text' => 'Gesamtzahl der registrierten Berechtigungen.',
            ],

            'unassigned' => [
                'heading' => 'Nicht zugewiesene Berechtigungen',
                'text' => 'Berechtigungen, die aktuell keiner Rolle zugewiesen sind.',
            ],
        ],

        'table' => [
            'columns' => [
                'assigned_roles' => 'Zugewiesene Rollen',
                'flags' => 'Flags',
                'sort' => 'Sortierung',
            ],
            'description' => 'Registrierte Berechtigungen, Guards, Kategorien und Rollenzuweisungen prüfen.',
            'empty' => 'Es sind noch keine Berechtigungen registriert.',
            'title' => 'Berechtigungsliste',
        ],
        'title' => 'Berechtigungsverwaltung',
    ],

    'roles' => [
        'actions' => [
            'create' => 'Erstellen',
        ],

        'badge' => [
            'color' => 'Farbe',
            'icon' => 'Symbol',
            'title' => 'Abzeichen',
            'variant' => 'Variante',
        ],
        'description' => 'Verwalten Sie die Metadaten von Rollen, die Sichtbarkeit von Zuweisungen und die Anzeigeeinstellungen für Rollenabzeichen.',

        'labels' => [
            'assignable_through_ui' => 'Über die Benutzeroberfläche zuweisbar',
            'assigned_users' => 'Zugewiesene Benutzer',
            'sort_order' => 'Sortierreihenfolge',
            'system_role' => 'Systemrolle',
        ],

        'messages' => [
            'not_allowed' => [
                'heading' => 'Nicht erlaubt',
                'only_super_admins_may_create_roles' => 'Nur Super-Admins dürfen Rollen erstellen.',
            ],

            'role_created' => [
                'heading' => 'Rolle angelegt',
                'text' => 'Die :role wurde erstellt.',
            ],

            'role_saved' => [
                'heading' => 'Rolle gespeichert',
                'text' => 'Die Metadaten der Rolle und die Badge-Einstellungen für :role wurden aktualisiert.',
            ],
        ],

        'modals' => [
            'create' => [
                'description' => 'Erstellen Sie eine neue zuweisbare Rolle für die Benutzer- und Rollenverwaltung.',
                'name_placeholder' => 'Support-Manager',
                'title' => 'Rolle erstellen',
            ],

            'edit' => [
                'title' => 'Rolle bearbeiten',
            ],
        ],

        'preview' => [
            'new_role' => 'Neue Rolle',
        ],
        'title' => 'Rollenverwaltung',
    ],
];
