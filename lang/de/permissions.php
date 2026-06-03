<?php

return [
    'admin' => [
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
    ],
];
