# Zehn Textkonflikte: Verwendung und Historie

Stand 16.10.2026. Ausschließlich lesend geprüft; keine Werte übernommen, freigegeben oder veröffentlicht.

## Gesicherter Befund

Alle zehn Konflikte haben Quellcode-Treffer außerhalb der ausgenommenen Altverzeichnisse. Dies belegt vorhandene Aufrufe, nicht die Erreichbarkeit jeder einzelnen View. Sprachdateiwerte und der frische Laravel-Translator im CLI stimmen für alle zehn Paare überein. Neun liefern den Legacy-Wert, nur `de / ui.type` den Foundation-Wert.

Für alle zehn Paare gibt es historische `lang_file_value_exported`-Ereignisse mit dem heutigen Foundation-Wert. Diese historischen Exportbelege sind keine aktuelle revisionsgebundene Wertfreigabe. Unter den zugehörigen Reviews sind keine neuen Wertfreigabe-/PublicationRunner-Belege vorhanden. Ein Key mit `review_status=reviewed` allein ersetzt diese nicht.

| Sprache / Key | Heutige Ausgabe | Fachliche Einordnung |
| --- | --- | --- |
| de / `ui.all.all-entries` | Alle | de: Foundation enthält ebenfalls englischen Text. Vorschlag „Alle Einträge“; bewusst in Workbench bearbeiten, nicht aus Legacy automatisch übernehmen. en: „All entries“ ist präziser als „All“. |
| de / `ui.all.all-types` | Alle | Foundation benennt ausdrücklich Typen; passt zum Typfilter. |
| de / `ui.button.activate_deactivate.activate_language` | Sprache aktivieren | Der Aufruf übergibt language. Foundation-Platzhalter :language ist erforderlich, damit die gewählte Sprache erscheint. |
| de / `ui.button.activate_deactivate.deactivate_language` | Deaktivieren: Sprache | Der Aufruf übergibt language. Foundation-Platzhalter :language ist erforderlich. |
| de / `ui.table.pagination.to` | zu | Zwischen Von-/Bis-Zahlen im Pagination-Text ist „bis“ passend. |
| de / `ui.type` | Typ | „Typ“ bereits wirksam; Legacy „Type“ weicht noch ab. |
| en / `admin.permission_list.filter.filter_by_category` | v | „v“ ist als Kategorienfilter-Titel unbrauchbar; Foundation „Filter by category“ passt. |
| en / `ui.all.all-entries` | All | de: Foundation enthält ebenfalls englischen Text. Vorschlag „Alle Einträge“; bewusst in Workbench bearbeiten, nicht aus Legacy automatisch übernehmen. en: „All entries“ ist präziser als „All“. |
| en / `ui.all.all-types` | All | Foundation benennt ausdrücklich Typen; passt zum Typfilter. |
| en / `ui.states.all-statuses` | All Statuses | Nur Groß-/Kleinschreibung verschieden; „All statuses“ konsistent als Satzschreibung. |

## Konkrete Fundstellen

- Sprachbuttons: `resources/views/components/ui/button/activate-deactivate.blade.php:24–25` übergeben den Platzhalter `language`.
- Pagination: `resources/views/components/ui/table/pagination.blade.php:50`.
- Kategorienfilter: `resources/views/components/admin/partials/permission-list/⚡filter.blade.php:80` und `role-list/⚡filter.blade.php:42`.
- Alle Einträge/Typen: TranslationList-Filter, zusätzlich Client-/HTML-Audit-Filter bei Typen.

Die vollständigen textuellen Quellcode-Treffer und Datensatz-IDs stehen im [Evidenzbericht](value-conflict-context-2026-10-16.json).

## Zeitliche Belege

| Key-ID | Ereignis-ID | Datum | Exportwert |
| ---: | ---: | --- | --- |
| 630 | 121364 | 2026-08-04 11:15:43 | de: All entries |
| 1428 | 121365 | 2026-08-04 11:15:43 | de: Alle Typen |
| 630 | 121369 | 2026-08-04 11:15:43 | en: All entries |
| 1428 | 121370 | 2026-08-04 11:15:43 | en: All types |
| 627 | 121371 | 2026-08-04 11:15:43 | en: All statuses |
| 3559 | 125591 | 2026-08-05 09:17:26 | de: :language aktivieren |
| 3558 | 125592 | 2026-08-05 09:17:26 | de: Deaktivieren: :language |
| 1801 | 129112 | 2026-08-06 07:30:38 | en: Filter by category |
| 3595 | 132232 | 2026-08-06 15:16:00 | de: bis |
| 656 | 138928 | 2026-10-14 04:55:42 | de: Typ |

Die heutigen Dateien enthalten neun dieser exportierten Werte nicht mehr. Der konkrete spätere Überschreibvorgang ist damit noch nicht nachgewiesen; aus den Zahlen darf keine bestimmte Person oder ein bestimmtes Kommando als Verursacher abgeleitet werden.

## Eigentümerschaft vor Veröffentlichung klären

`ui.type` hat zwei nicht obsolete Key-Datensätze (656 und 1212), zusätzlich historische obsolete Keys. Deshalb darf keine automatische Auswahl nach erster ID erfolgen. Die anderen sieben unterschiedlichen Keys besitzen jeweils einen nicht obsoleten Key-Datensatz. Die Tabelle `key_values` enthält für diese Keys keine Zeilen; die verglichenen aktuellen Texte liegen in `lang_values`. Vor einer Veröffentlichung müssen der zuständige aktive Finding-Kontext und der aktuelle Freigabevertrag zusammenpassen.

Nächster Schritt: diese zehn Fälle als konkrete Workbench-Korrekturen vorbereiten, dabei die doppelte aktive Zuordnung von `ui.type` klären und die deutsche Form von „All entries“ bewusst korrigieren. Danach revisionsgebundene Freigabe und gezielte Veröffentlichung über den gemeinsamen Dienst; keine direkte Bearbeitung der Sprachdateien oder pauschale Legacy-Übernahme.

## Grenzen / Wiederholung

`php docs/audits/translation-workbench-2026-10-15/inspect-value-conflicts.php` liest den dann aktuellen Bestand in einer PostgreSQL-Read-only-Transaktion. Anschließend werden die Sprachdateien und der CLI-Translator geprüft. Datenbank und Dateisystem sind kein gemeinsamer atomarer Snapshot; Dateiprüfsummen sind dokumentiert. Keine Browser-Abnahme und keine Aussage über bestehende Browser-/Worker-Caches.
