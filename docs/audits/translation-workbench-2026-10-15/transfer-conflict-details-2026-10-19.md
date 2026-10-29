# Prüfung der zehn Wertkonflikte

Aktuelle schreibgeschützte Abfrage: 2026-10-19T09:25:28+02:00. Ursprünglicher Snapshot: 2026-10-19T07:41:38+02:00.

Alle zehn aktuellen Evidenzmengen stimmen weiterhin mit dem Snapshot überein. Alle gelesenen Sprachdateien blieben während des Lesens unverändert.
Die Datenbankabfrage lief in einer PostgreSQL-Transaktion mit REPEATABLE READ und READ ONLY; anschließend ROLLBACK. Sprachdateien wurden statisch geparst, nicht ausgeführt.

**Keine Übersetzungen geändert, keine Freigaben erteilt, keine Findings/Keys zusammengeführt.** Die folgenden Empfehlungen sind fachliche Prüfhinweise, keine Importentscheidungen.

| Locale | Key | Legacy | Workbench | Sprachdatei | Einordnung |
| --- | --- | --- | --- | --- | --- |
| de | ui.all.all-entries | Alle | All entries | Alle | Sprachfehler im Workbench-Wert |
| de | ui.all.all-types | Alle | Alle Typen | Alle | Textpräzisierung |
| de | ui.button.activate_deactivate.activate_language | Sprache aktivieren | :language aktivieren | Sprache aktivieren | Platzhalter fehlt in der Datei |
| de | ui.button.activate_deactivate.deactivate_language | Deaktivieren: Sprache | Deaktivieren: :language | Deaktivieren: Sprache | Platzhalter fehlt in der Datei |
| de | ui.table.pagination.to | zu | bis | zu | Unpassendes Wort im Bereich |
| de | ui.type | Type | Typ | Typ | Nur Legacy-Wert abweichend; separater Strukturpunkt |
| en | admin.permission_list.filter.filter_by_category | v | Filter by category | v | Offensichtlich unpassender Dateitext |
| en | ui.all.all-entries | All | All entries | All | Textpräzisierung |
| en | ui.all.all-types | All | All types | All | Textpräzisierung |
| en | ui.states.all-statuses | All Statuses | All statuses | All Statuses | Nur Groß-/Kleinschreibung |

## de · ui.all.all-entries

„All entries“ ist im deutschen Slot englisch. „Alle Einträge“ als fachlichen Vorschlag prüfen; „Alle“ bleibt ebenfalls eine mögliche UI-Formulierung. Keinen der vorhandenen Werte automatisch übernehmen.

Review-ID: `3a6ba8b355fbb67f`. Aktive Finding-Verweise laut Datenbank: 2.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 7655 | "Alle" |
| workbench | 1736 | "All entries" |
| file | lang/de/ui.php | "Alle" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #8329: `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:159`
- Finding #8331: `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:425`

## de · ui.all.all-types

„Alle Typen“ benennt den Filterinhalt genauer. Entscheidung zusammen mit der englischen Variante treffen.

Review-ID: `2d6e2e67f08c5ff9`. Aktive Finding-Verweise laut Datenbank: 3.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 7664 | "Alle" |
| workbench | 1740 | "Alle Typen" |
| file | lang/de/ui.php | "Alle" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #8325: `resources/views/components/admin/partials/client-list/⚡filter.blade.php:67`
- Finding #8328: `resources/views/components/admin/partials/html-view-audit/⚡filter.blade.php:115`
- Finding #8333: `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:470`

## de · ui.button.activate_deactivate.activate_language

Der aktuelle Komponentenaufruf übergibt language. Workbench „:language aktivieren“ erhält diese Information; Datei „Sprache aktivieren“ verwendet sie nicht. Workbench-Wert zur Freigabe vorschlagen.

Review-ID: `2f3678707683a2e6`. Aktive Finding-Verweise laut Datenbank: 1.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 6607 | "Sprache aktivieren" |
| workbench | 674 | ":language aktivieren" |
| file | lang/de/ui.php | "Sprache aktivieren" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #2366: `resources/views/components/ui/button/activate-deactivate.blade.php:25`

## de · ui.button.activate_deactivate.deactivate_language

Auch dieser Aufruf übergibt language. Workbench „Deaktivieren: :language“ erhält diese Information. Workbench-Wert zur Freigabe vorschlagen.

Review-ID: `2910e45a656b284a`. Aktive Finding-Verweise laut Datenbank: 1.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 6489 | "Deaktivieren: Sprache" |
| workbench | 675 | "Deaktivieren: :language" |
| file | lang/de/ui.php | "Deaktivieren: Sprache" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #2365: `resources/views/components/ui/button/activate-deactivate.blade.php:24`

## de · ui.table.pagination.to

Im Code steht das Wort zwischen firstItem und lastItem: „1 bis 10“. Workbench „bis“ passt; Datei „zu“ nicht. Workbench-Wert zur Freigabe vorschlagen.

Review-ID: `5b80abfbeaa0cfb6`. Aktive Finding-Verweise laut Datenbank: 1.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 6508 | "zu" |
| workbench | 715 | "bis" |
| file | lang/de/ui.php | "zu" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #2389: `resources/views/components/ui/table/pagination.blade.php:50`

## de · ui.type

Datei und Workbench enthalten „Typ“, Legacy „Type“. „Typ“ als Inhalt erhalten. Den zweistufigen Key ui.type separat über die UI prüfen; keine automatische Umbenennung in ui.type.type und keine pauschale Zusammenlegung von Findings.

Review-ID: `d3255e58569e8f1a`. Aktive Finding-Verweise laut Datenbank: 387.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 8321 | "Type" |
| workbench | 1785 | "Typ" |
| file | lang/de/ui.php | "Typ" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #6007: `resources/views/components/admin/partials/activity-log/table/⚡active-filter-badges.blade.php:9`
- Finding #6009: `resources/views/components/admin/partials/activity-log/table/⚡all.blade.php:94`
- Finding #6022: `resources/views/components/admin/partials/client-list/⚡filter.blade.php:49`
- Finding #6032: `resources/views/components/admin/partials/client-list/⚡table.blade.php:53`
- Finding #6033: `resources/views/components/admin/partials/client-list/⚡table.blade.php:56`
- Vollständige Liste in der JSON-Datei; keine erneute Erfassung oder Positionskorrektur durchgeführt.

## en · admin.permission_list.filter.filter_by_category

Der Key wird als Titel der Kategorieauswahl verwendet. Workbench „Filter by category“ passt zum Kontext; Datei/Legacy „v“ nicht. Workbench-Wert zur Freigabe vorschlagen.

Review-ID: `a417059e2aa4fd45`. Aktive Finding-Verweise laut Datenbank: 2.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 1444 | "v" |
| workbench | 993 | "Filter by category" |
| file | lang/en/admin.php | "v" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #1276: `resources/views/components/admin/partials/role-list/⚡filter.blade.php:42`
- Finding #3914: `resources/views/components/admin/partials/permission-list/⚡filter.blade.php:80`

## en · ui.all.all-entries

„All“ und „All entries“ sind beide denkbar; „All entries“ macht die Filterbedeutung explizit. Gemeinsam mit der deutschen Variante entscheiden.

Review-ID: `383c6ef1ed3a4760`. Aktive Finding-Verweise laut Datenbank: 2.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 7663 | "All" |
| workbench | 1735 | "All entries" |
| file | lang/en/ui.php | "All" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #8329: `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:159`
- Finding #8331: `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:425`

## en · ui.all.all-types

„All types“ macht die Filterbedeutung explizit. Gemeinsam mit der deutschen Variante entscheiden.

Review-ID: `bb25614e437bd4c0`. Aktive Finding-Verweise laut Datenbank: 3.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 7672 | "All" |
| workbench | 1739 | "All types" |
| file | lang/en/ui.php | "All" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #8325: `resources/views/components/admin/partials/client-list/⚡filter.blade.php:67`
- Finding #8328: `resources/views/components/admin/partials/html-view-audit/⚡filter.blade.php:115`
- Finding #8333: `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:470`

## en · ui.states.all-statuses

„All Statuses“ gegenüber „All statuses“. Eine UI-Schreibkonvention wählen; keine technische Datenkorrektur erzwingen.

Review-ID: `7bcc0fe503561119`. Aktive Finding-Verweise laut Datenbank: 10.

| Quelle | ID / Datei | Aktueller Wert |
| --- | --- | --- |
| legacy | 8293 | "All Statuses" |
| workbench | 1737 | "All statuses" |
| file | lang/en/ui.php | "All Statuses" |

Kontextverweise (DB-Positionen; keine neue Scannerbestätigung):

- Finding #8316: `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-key-values/status.blade.php:18`
- Finding #8317: `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/dynamic-source-values/status.blade.php:20`
- Finding #8318: `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/findings/status.blade.php:20`
- Finding #8319: `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-findings/status.blade.php:20`
- Finding #8320: `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/key-values/status.blade.php:18`
- Vollständige Liste in der JSON-Datei; keine erneute Erfassung oder Positionskorrektur durchgeführt.

## Geprüfter Quellcode-Kontext

- `resources/views/components/ui/button/activate-deactivate.blade.php:24` und `:25`: übergeben `language` an den Übersetzungsaufruf.
- `resources/views/components/ui/table/pagination.blade.php:50`: zwischen erstem und letztem Ergebniseintrag.
- `resources/views/components/admin/partials/permission-list/⚡filter.blade.php:80` und `role-list/⚡filter.blade.php:42`: Titel der Kategorieauswahl.
- `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:159`, `:425`, `:470`: Verwendung der All-Filter.

## Konsequenz für die Übernahme

Alle unterschiedlichen Texte samt Herkunft bleiben als Kandidaten erhalten. Die vier kontextuell klaren Workbench-Vorschläge (Kategorie, Pagination, zwei Sprachbuttons) können gezielt zur Freigabe vorgelegt werden. Der deutsche All-entries-Wert braucht eine bewusste Textkorrektur; die übrigen Filtertexte eine redaktionelle Entscheidung. ui.type benötigt getrennte Inhalts- und Strukturbehandlung.
Eine spätere Freigabe muss den dann aktuellen Datenstand erneut prüfen. Diese Untersuchung deckt ausschließlich die zehn bekannten Wertkonflikte ab, nicht die 16 NULL-Nachweise oder sämtliche DB-only-Werte.
