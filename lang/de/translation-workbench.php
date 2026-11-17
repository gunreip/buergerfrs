<?php

return [
    'modals' => [
        'source' => [
            'source-evidence' => 'Quellenangaben',
        ],
        'label' => [
            'set-input-select-language' => 'Eingabe einstellen / Sprache auswählen',
        ],
        'publishing_language_files' => 'Publishing language files …',
    ],
    'tw-graph-diagnostics' => [
        'summary' => [
            'fail-summary-failed' => [
                'please-copy-the-list-manually' => 'Fehler: Das Kopieren der Zusammenfassung ist fehlgeschlagen. Bitte wählen Sie die Liste manuell aus und kopieren Sie sie.',
            ],
        ],
    ],
    'overview' => [
        'recent-work' => [
            'recently-edited' => 'Zuletzt bearbeitet',
        ],
    ],
    'tab' => [
        'in-progress' => [
            'content' => [
                'started_work_ordered_by_the_last_manual_edit_status_reflects_saved_revisions_and' => 'Der Vorgang wurde gestartet, basierend auf der letzten manuellen Bearbeitung. Der Status spiegelt gespeicherte Überarbeitungen und erfasste Scans wider; bei „Fortsetzen“ wird der aktuelle Arbeitsablauf erneut überprüft.',
            ],
        ],
    ],
    'workflow' => [
        'progress' => [
            'the_scanner_recorded_this_source_occurrence_its_position_and_the_captured_source' => 'Der Scanner hat dieses Vorkommen der Quelle, deren Position und die erfassten Quellenangaben aufgezeichnet. Dabei wird keine Übersetzung zugeordnet oder veröffentlicht.',
        ],
    ],
    'filter' => [
        'toggle' => [
            'match_case' => 'Groß-/Kleinschreibung beachten',
            'whole_word_or_segment' => 'Ganze Wörter oder Wortteile',
        ],
    ],
    'testcase' => [
        'button' => [
            'testcase-on' => 'Testumgebung EIN',
            'testcase-off' => 'Testumgebung AUS',
        ],
        'literals' => [
            'each_activation_starts_with_fresh_test_datasets_saving_only_changes_the_test_database' => 'Jede Aktivierung beginnt mit neuen Testdatensätzen. Beim Speichern werden lediglich die Testdatenbank und die Testdateien geändert.',
        ],
    ],
    'translation' => [
        'key' => [
            'translation-key' => 'Übersetzungsschlüssel',
            'translation-key-filters' => 'Übersetzungsschlüssel-Filter',
        ],
        'findings' => [
            'code-update-plan' => [
                'alredy-current' => 'Bereits aktuell',
            ],
        ],
    ],
    'buttons' => [
        'run_new_scan' => 'Neuen Scan starten',
        'open_last_scan' => 'Letzten Scan öffnen',
        'tooltips' => [
            'source_files_were_added_changed_or_removed_or_could_not_be_checked' => 'Quelldateien wurden hinzugefügt, geändert oder entfernt, oder konnten nicht überprüft werden.',
            'the_last_scan_has_reached_the_configured_age_limit' => 'Der letzte Scan hat die festgelegte Altersgrenze erreicht.',
        ],
        'new_scan_required' => 'Neuer Scan erforderlich',
    ],
    'edits' => [
        'edit_translations' => 'Übersetzungen bearbeiten',
    ],
    'literal' => [
        'source_file' => 'Source-Code Datei',
        'translation-workbench' => [
            'translation_workbench_overview' => 'Translation Workbench Übersicht',
        ],
        'stored_positions_for_findings_in_source_files_no_longer_match_or_could_not_be_verified' => 'Die gespeicherten Positionen für Fundstellen in den Quelldateien stimmen nicht mehr überein oder konnten nicht überprüft werden.',
        'stored_finding_positions_need_to_be_checked_by_a_new_scan' => 'Gespeicherte Fundorte müssen durch einen neuen Scan überprüft werden.',
        'finding_old_archived_as_a_duplicate_finding_active_remains_active' => 'Treffer Nr. :old wurde als Duplikat archiviert. Treffer Nr. :active bleibt aktiv.',
        'use_run_scan_to_preview_changes_and_then_save_the_checked_scan_results_source_replace' => 'Verwenden Sie „Scan ausführen“, um eine Vorschau der Änderungen anzuzeigen, und speichern Sie anschließend die markierten Scanergebnisse. Die Quelldateiersetzung bleibt für die betroffenen Dateien gesperrt, bis die Diskrepanz behoben ist.',
        'runtime_value_contains_text_that_requires_translation' => 'Der Laufzeitwert enthält Text, der übersetzt werden muss.',
        'open_shows_active_findings_that_still_need_work_for_the_active_target_language_enable' => '„Offen“ zeigt aktive Ergebnisse an, die für die jeweilige Zielsprache noch bearbeitet werden müssen. Aktivieren Sie „Nicht zugewiesen“, um sich auf Ergebnisse ohne Schlüsselzuordnung zu konzentrieren. Bearbeiten Sie die Übersetzungswerte unter „Übersetzungen“.',
        'your_five_most_recently_edited_keys_including_completed_work_scanner_events_do_not_ch' => 'Ihre fünf zuletzt bearbeiteten Schlüssel, einschließlich abgeschlossener Arbeiten. Scanner-Ereignisse haben keinen Einfluss auf diese Reihenfolge.',
        'active_findings_linked_to_keys_with_a_completed_publication_each_source_occurrence_is' => 'Aktive Funde, die mit Übersetzungs-Schlüsseln verknüpft sind, für die eine Veröffentlichung vorliegt. Jedes Vorkommen der Quelle wird separat aufgeführt. Eine Veröffentlichung allein bedeutet nicht, dass die Quelle bereits ersetzt werden kann.',
        'this_source_occurrence_is_no_longer_active' => 'Dieses Vorkommen ist nicht mehr aktiv.',
        'the_displayed_page_is_checked_against_the_current_source_when_loaded_positions_are_ca' => 'Die angezeigte Seite wird beim Laden mit der aktuellen Quelldatei abgeglichen. Die Positionen sind erfasste Scan-Positionen und keine automatisch korrigierten Stellen. Unter „Quelle vergleichen“ können Sie eine Verschiebung oder Löschung explizit mit einer Begründung bestätigen. Die Vorschau zum Ersetzen der Quelle überprüft ein ausgewähltes Vorkommen erneut. Unter „Vorbereiten“ wird ein Plan gespeichert; erst die anschließende Bestätigung schreibt die Quelldatei.',
    ],
    'header' => [
        'source_replacement' => 'Ersetzung im Quellcode',
    ],
    'badge' => [
        'workflow_closed' => 'Workflow abgeschlossen',
    ],
];
