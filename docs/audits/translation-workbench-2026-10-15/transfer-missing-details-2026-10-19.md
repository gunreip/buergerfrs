# Prüfung der 16 fehlenden Wertnachweise

Schreibgeschützte Abfrage: 2026-10-19T09:32:25+02:00. Audit-Ausgangsstand: 2026-10-19T07:41:38+02:00.

Alle 16 aktuellen Evidenzmengen sind unverändert. Die Lücke ist jeweils ein Legacy-Wert mit Status ok und Inhalt NULL; NULL wird ausdrücklich nicht als leerer Übersetzungstext interpretiert.

## Ergebnis

- Zwei Locale-/Key-Kombinationen besitzen bereits übereinstimmende aktive Workbench- und Dateitexte. Hier fehlt keine aktuelle Übersetzung.
- 14 Kombinationen (sieben Keys × de/en) besitzen ausschließlich Legacy-NULL als aktuellen Kandidaten. Ihre früheren Texte existieren noch in obsolete Workbench-Einträgen. Es gibt keine aktiven Finding-Verweise auf diese sieben Keys in der geprüften Datenbank.
- Keine Daten geändert, keine obsolete Einträge reaktiviert und keine Texte aus Key-Namen erfunden.

| Locale | Key | Legacy-ID (ok / NULL) | Workbench-ID / Status | Gespeicherter Workbench-Text | In aktueller Sprachdatei |
| --- | --- | --- | --- | --- | --- |
| de | admin.permissions.filters.assignment.label | #5 | #214 / obsolete | "Zuweisung" | kein Eintrag |
| de | admin.permissions.filters.system.label | #17 | #220 / obsolete | "System" | kein Eintrag |
| de | admin.permissions.filters.title | #19 | #2100 / obsolete | "Filter" | kein Eintrag |
| de | admin.permissions.labels.sort_order | #23 | #223 / obsolete | "Sortierreihenfolge" | kein Eintrag |
| de | admin.permissions.modals.edit.title | #53 | #238 / active | "Berechtigung bearbeiten" | ja, identisch |
| de | admin.permissions.overview.title | #83 | #253 / obsolete | "Übersicht" | kein Eintrag |
| de | admin.permissions.table.columns.assigned_roles | #93 | #258 / obsolete | "Zugewiesene Rollen" | kein Eintrag |
| de | ui.actions.save | #107 | #2032 / obsolete | "Speichern" | kein Eintrag |
| en | admin.permissions.filters.assignment.label | #6 | #1016 / obsolete | "Assignment" | kein Eintrag |
| en | admin.permissions.filters.system.label | #18 | #1022 / obsolete | "System" | kein Eintrag |
| en | admin.permissions.filters.title | #20 | #2102 / obsolete | "Filtering" | kein Eintrag |
| en | admin.permissions.labels.sort_order | #24 | #1025 / obsolete | "Sort order" | kein Eintrag |
| en | admin.permissions.modals.edit.title | #54 | #1040 / active | "Edit Permission" | ja, identisch |
| en | admin.permissions.overview.title | #84 | #1055 / obsolete | "Overview" | kein Eintrag |
| en | admin.permissions.table.columns.assigned_roles | #94 | #1060 / obsolete | "Assigned roles" | kein Eintrag |
| en | ui.actions.save | #108 | #2035 / obsolete | "Save" | kein Eintrag |

## Die zwei bereits abgedeckten Fälle

admin.permissions.modals.edit.title ist in de als „Berechtigung bearbeiten“ und in en als „Edit Permission“ vorhanden. Aktive Workbench-Werte #238/#1040 und lang/de/admin.php beziehungsweise lang/en/admin.php stimmen überein. Der aktive Finding-Verweis ist #1301. Nur Legacy #53/#54 enthält NULL.
Diese Legacy-Lücke darf eine spätere Übernahme vorhandener Texte nicht durch einen leeren Wert ersetzen. Das ist ein Hinweis für die Übernahmeregel, keine hier erteilte Freigabe.

## Kontextprüfung der übrigen Keys

Die aktuelle Textsuche hat 1684 Pfade aus dem gespeicherten Manifest nach Abzug der zwölf Archivdateien berücksichtigt. Fehlende Pfade: 0. Seit dem Snapshot inhaltlich geänderte Dateien: 1.
Neu hinzugekommene, im Manifest noch unbekannte Dateien sind damit nicht abgedeckt. Diese Suche ersetzt weder eine Parserprüfung noch einen neuen Scannerlauf. Ein Fund des Key-Texts ist nicht automatisch ein Übersetzungsaufruf.

- `admin.permissions.filters.assignment.label`: 0 Texttreffer.
- `admin.permissions.filters.system.label`: 0 Texttreffer.
- `admin.permissions.filters.title`: 0 Texttreffer.
- `admin.permissions.labels.sort_order`: 0 Texttreffer.
- `admin.permissions.overview.title`: 1 Texttreffer.
  - `packages/gunreip/laravel-translation-workbench/src/Livewire/TwGraphDataDrivenDatasets.php:18`: 266 =&gt; 'Rekey target · admin.permissions.overview.title',
- `admin.permissions.table.columns.assigned_roles`: 0 Texttreffer.
- `ui.actions.save`: 2 Texttreffer.
  - `packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/translation-migration.blade.php:131`: 'text' =&gt; ['shared translation key', 'ui.actions.save'],
  - `packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/translation-migration.blade.php:145`: 'text' =&gt; ['ui.actions.save', 'becomes ui.button.save.primary'],

Die Treffer für overview.title sind ein beschreibender Critical-Datasets-Eintrag. Die beiden Treffer für ui.actions.save sind TW-Graph-Beispielbeschriftungen. Sie belegen keinen aktiven Übersetzungsaufruf. ui.actions.save_changes ist ein anderer Key und wird nicht mitgezählt.

## Konsequenz für den Neustart

1. Die zwei vorhandenen Übersetzungen als vollständige Textkandidaten mit Herkunft erhalten; Legacy-NULL nicht als konkurrierenden Text behandeln.
2. Die sieben historischen Keys nicht automatisch neu erfassen, freigeben oder reaktivieren. Ihre alten Texte bleiben als Archivnachweis erhalten.
3. Erst der geplante vollständige Erstscan bestimmt aktuelle Fundstellen. Falls ein solcher Key tatsächlich wieder verwendet wird, ist das anhand der neuen Erfassung bewusst zu bearbeiten.
4. Anschließend die 122 DB-only-Kombinationen separat prüfen: Dort gibt es echte Textnachweise, die bei einem Reset nicht verloren gehen dürfen.

Nachweise: transfer-missing-details-2026-10-19.json; Abfrage über inspect-transfer-conflicts.php missing_value_evidence. Vollständige Dateihashes und Datenbank-IDs sind im JSON enthalten.
