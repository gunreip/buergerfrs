# Übersetzungswerte: Legacy, Workbench und Sprachdateien

Stand: 13.10.2026. Rein lesende Momentaufnahme des aktuellen lokalen Bestands.

## Ergebnis

Verglichen wurden **8.284 Legacy-Wertzeilen**, **2.216 Workbench-Sprachwertzeilen** und **54 PHP-/JSON-Sprachdateien mit 1.392 Blattwerten**. Der Schlüssel für den Vergleich ist der vollständige Übersetzungskey zusammen mit der normalisierten Locale.

| Kategorie | Key-/Locale-Paare |
|---|---:|
| Identische Werte | 1,377 |
| Unterschiedliche Werte | 422 |
| Nur Legacy | 6,302 |
| Mehrdeutige Zuordnung | 81 |
| Nur Workbench | 336 |
| **Gesamt** | **8,518** |

Die Kategorien bezeichnen vorhandene Datensätze, nicht automatisch verfügbare oder freigegebene Übersetzungen. Auch leere und obsolete Werte sind enthalten. Mehrere Zeilen für dasselbe Key-/Locale-Paar werden nicht willkürlich auf eine Zeile reduziert.

### Die 422 Abweichungen sind nicht 422 konkurrierende Texte

- **412 Paare:** Legacy ist leer, Workbench enthält einen Text. Der Status muss vor Übernahme gesondert bewertet werden.
- **10 Paare:** Beide Seiten enthalten unterschiedliche, nichtleere Texte. Alle zehn haben Legacy-Wertstatus `ok` und Workbench-Wertstatus `active`.
- Bei diesen zehn Paaren entspricht der direkt vorhandene Sprachdateiwert jeweils dem Legacy-Wert.

| Locale | Key | Legacy / Sprachdatei | Workbench |
|---|---|---|---|
| de | `ui.all.all-entries` | Alle | All entries |
| de | `ui.all.all-types` | Alle | Alle Typen |
| de | `ui.button.activate_deactivate.activate_language` | Sprache aktivieren | :language aktivieren |
| de | `ui.button.activate_deactivate.deactivate_language` | Deaktivieren: Sprache | Deaktivieren: :language |
| de | `ui.table.pagination.to` | zu | bis |
| de | `ui.type` | Type | Typ |
| en | `admin.permission_list.filter.filter_by_category` | v | Filter by category |
| en | `ui.all.all-entries` | All | All entries |
| en | `ui.all.all-types` | All | All types |
| en | `ui.states.all-statuses` | All Statuses | All statuses |

Diese Tabelle ist eine Entscheidungsliste, keine automatische Empfehlung, eine bestimmte Seite zu übernehmen. Beispielsweise enthalten zwei Workbench-Texte `:language` als Platzhalter; das braucht zusätzlich einen Abgleich mit dem jeweiligen Aufruf.

### Nur Legacy / nur Workbench

Die **6.302 nur in Legacy vorhandenen Paare** umfassen 6.323 Zeilen:

- **6.292 leere Zeilen** mit Status `missing`, überwiegend Sprachvarianten.
- **21 nichtleere Zeilen** mit Status `ok`, sämtlich `is_base_duplicate=true`.
- **10 nichtleere Zeilen** mit Status `obsolete`.

Die 21 Base-Duplicates betreffen drei Keys für jeweils sieben deutsche Sprachvarianten: `admin.translation_list.modal_history.event`, `admin.translation_list.modal_history.events` und `admin.translation_list.table.history`. Ihr Fehlen als eigener Dateiwert passt zum bestehenden Exportausschluss für Base-Duplicates. Die tatsächliche Fallback-Auflösung wurde hier nicht nachgetestet.

Die **336 nur in Workbench vorhandenen Paare** enthalten 206 aktive und 130 obsolete nichtleere Werte. Auch diese Gruppen dürfen nicht gemeinsam ungeprüft exportiert werden.

## Verteilung nach Sprache

| Locale | Identisch | Abweichend | Nur Legacy | Nur Workbench | Mehrdeutig |
|---|---:|---:|---:|---:|---:|
| de | 685 | 209 | 14 | 168 | 5 |
| de-at | 0 | 1 | 897 | 0 | 10 |
| de-be | 0 | 1 | 898 | 0 | 9 |
| de-ch | 0 | 1 | 897 | 0 | 10 |
| de-de | 0 | 1 | 893 | 0 | 14 |
| de-it | 0 | 1 | 897 | 0 | 10 |
| de-li | 0 | 1 | 898 | 0 | 9 |
| de-lu | 0 | 1 | 898 | 0 | 9 |
| en | 692 | 206 | 10 | 168 | 5 |

## Status und Reviews: getrennte Prüfmerkmale

Für die 1.799 beidseitig eindeutigen Paare wurden zusätzliche Unterschiede erfasst:

| Prüfmerkmal | Paare | Bedeutung |
|---|---:|---|
| Unterschiedlicher Wertstatus | 509 | Legacy `ok` wird für den Vergleich als `active` eingeordnet; andere Status bleiben getrennt. |
| Unterschiedlicher Key-Obsolete-Zustand | 24 | Nur bei genau einem zugehörigen Workbench-Key ausgewertet. |
| Unterschiedliche Existenz eines Key-Review-Zeitpunkts | 257 | Hinweis auf verschiedene Review-Historien, kein Beweis einer falschen Freigabe. |
| Workbench-Key-Zuordnung nicht genau eins | 704 | Davon 294 ohne zugehörigen Key, 410 mit mehreren Key-Datensätzen. Mehrere Datensätze können fachlich durch verschiedene Findings/Scopes entstehen. |

Merkmale überschneiden sich und werden **nicht addiert**. Bei 577 der 1.377 textgleichen Paare liegt mindestens eines dieser Prüfmerkmale vor. Textgleichheit bedeutet daher nicht, dass auch Status und Historie übereinstimmen.

Zusätzlich gibt es in insgesamt 102 Paaren mehrere DB-Wertzeilen. 81 davon stehen auf beiden Seiten und bilden deshalb die Kategorie „Mehrdeutig“; 21 gehören zu „Nur Legacy“. Die Einzel-IDs bleiben im Detailbericht erhalten.

Workbench-Reviews und Legacy-Reviews sind strukturell verschieden. Der Bericht speichert vorhandene Review-Metadaten, bildet aber keine künstliche Gleichsetzung sämtlicher Review-Entscheidungen ab. Es findet keine Auswahl nach „neuestem Zeitstempel gewinnt“ statt.

## Sprachdateien

| Vergleich pro DB-Wertzeile | Identisch | Abweichend | Kein direkter Dateieintrag |
|---|---:|---:|---:|
| Legacy → Sprachdatei | 1.473 | 0 | 6.811 |
| Workbench → Sprachdatei | 1.382 | 10 | 824 |

Die fehlenden Dateieinträge enthalten auch leere, obsolete und absichtlich nicht exportierte Werte. Insbesondere sind die 6.811 Legacy-Zeilen kein Beleg für 6.811 fehlende sichtbare Übersetzungen.

Unter den Workbench-Werten ohne direkten Dateieintrag befinden sich **462 aktive, nichtleere Werte**. Für **460** davon existiert mindestens ein nicht-obsoleter Workbench-Key; **2** haben keinen solchen Key. Diese 460 sind die nächste sinnvolle Prüfliste für Veröffentlichung, noch kein freigegebener Exportplan. Weitere Export-Konflikte oder Pruning-Wirkungen wurden nicht durch einen Exportlauf geprüft.

## Methode und Grenzen

- DB-Snapshot in einer PostgreSQL-Transaktion mit `REPEATABLE READ, READ ONLY`; anschließend Rollback. Keine schreibenden Commands oder Exporter aufgerufen.
- Locale-Vergleich: Kleinbuchstaben und `_` → `-`. Ursprüngliche Locale bleibt in den Rohdaten erhalten. Übersetzungskeys werden nicht umbenannt oder umgeschrieben.
- Texte werden exakt verglichen, ohne Trimmen oder inhaltliche Normalisierung. `null` und Leerstring werden beim eigentlichen Wertvergleich unterschieden.
- Workbench-Sprachwerte stammen aus `translation_workbench_lang_values`, dem Ziel des aktuellen Sprachwert-Editors. `translation_workbench_key_values` enthält aktuell 0 Zeilen. Die 200 `translation_workbench_dynamic_key_values` werden **nicht** als normale Locale-Sprachwerte eingeordnet; sie benötigen einen separaten fachlichen Vergleich. Ältere Package-`values` sind ebenfalls nicht Teil dieses Legacy/Foundation-Sprachwertvergleichs.
- 14.205 Legacy-Keys und 5.678 Workbench-Keys wurden als Kontext gelesen; Keys ohne Sprachwertzeile werden nicht zu fiktiven Locale-Paaren erweitert.
- PHP-Spracharrays und JSON-Dateien unter `lang/` wurden gelesen. `lang/vendor` bleibt außen vor. SHA-256-Hashes dokumentieren die 54 Dateien; beim Lesen wurden keine Änderungen erkannt.
- Verglichen werden direkte Dateieinträge, keine vollständige Laravel-Laufzeitauflösung mit Fallbacks, Ersatzparametern, Pluralisierung oder Package-Namespaces. Der DB-Snapshot und das anschließende Dateilesen sind kein gemeinsamer atomarer Snapshot.
- Der Legacy-Dateiabgleich nimmt den vollständigen Key; Workbench zusätzlich den erwarteten PHP-Dateipfad. In dieser Momentaufnahme gab es keine mehrdeutigen Dateitreffer.
- Alle 54 Dateien konnten gelesen werden. Die Summen wurden gegen die erhaltenen Einzelzeilen geprüft. Die Prüfung ersetzt keine vollständigen Workflow-/Migrationstests.

## Detaildaten und Reproduktion

Die vollständigen Übersetzungswerte und Herkunfts-/Review-Informationen liegen lokal außerhalb der versionierten Dokumentation:

- [comparison.json](../storage/app/private/translation-value-comparison-20261013-053747/comparison.json) — Vollständiger Snapshot mit Einzelzeilen, Status, Quellen, Review-Kontext und Datei-Hashes.
- [comparison.csv](../storage/app/private/translation-value-comparison-20261013-053747/comparison.csv) — Tabellarische Vergleichsliste für alle Key-/Locale-Paare.
- [audit.php](../storage/app/private/translation-value-comparison-20261013-053747/audit.php) — Reproduzierbares lesendes Erhebungsskript.

## Nächster Schritt

1. Die zehn echten Textkonflikte fachlich entscheiden und vorhandene Platzhalter mit den Aufrufen abgleichen.
2. Die 460 aktiven Workbench-Werte mit nicht-obsoletem Key und fehlendem Dateieintrag auf Verwendung und Exportfähigkeit prüfen, ohne dabei pauschales Pruning auszuführen.
3. Mehrdeutige Zuordnungen und Statuskonflikte auflösen; Base-Duplicate-/Fallback-Verhalten bewahren.
4. Erst danach einen getrennten, prüfbaren Übernahme-/Veröffentlichungsplan erstellen. In diesem Schritt wurden keine Übersetzungen oder Statuswerte verändert.

## Aktualisierte Prüfliste nach wiederholbarem Datenbankvergleich

Erhoben am 2026-10-13T15:19:24+02:00. Der neue Command wurde rein lesend auf dem lokalen PostgreSQL-Bestand ausgeführt. Sprachdateien und Laufzeit-Fallbacks wurden diesmal nicht erneut ausgewertet. Die Hauptzahlen stimmen mit der obigen Momentaufnahme überein.

| Kategorie | Paare |
|---|---:|
| identical | 1377 |
| different | 422 |
| legacy_only | 6302 |
| workbench_only | 336 |
| ambiguous | 81 |

### Zehn nichtleere Textkonflikte: konkrete Prüfung

Alle zehn haben Legacy-Status `ok` und Workbench-Status `active`. Folgende Einschätzungen sind Vorschläge aus dem Quellcode-Kontext, **keine ausgeführten Änderungen oder Freigaben**.

| Locale | Key | Legacy (Wert-ID) | Workbench (Wert-ID) | Einschätzung |
|---|---|---|---|---|
| de | `ui.all.all-entries` | Alle (7655) | All entries (1736) | Workbench-Text prüfen/übernehmen: vollständiger Filterbegriff; Deutsch muss noch übersetzt werden. |
| de | `ui.all.all-types` | Alle (7664) | Alle Typen (1740) | Workbench-Text „Alle Typen“ / „All types“ passt zum Typfilter. |
| de | `ui.button.activate_deactivate.activate_language` | Sprache aktivieren (6607) | :language aktivieren (674) | Workbench bevorzugt: Aufruf übergibt bereits language. |
| de | `ui.button.activate_deactivate.deactivate_language` | Deaktivieren: Sprache (6489) | Deaktivieren: :language (675) | Workbench bevorzugt: Aufruf übergibt bereits language. |
| de | `ui.table.pagination.to` | zu (6508) | bis (715) | Workbench „bis“ passt zum Bereich zwischen erster/letzter Trefferzahl. |
| de | `ui.type` | Type (8321) | Typ (1785) | Workbench „Typ“ passt zu Spalten- und Filterbeschriftungen. |
| en | `admin.permission_list.filter.filter_by_category` | v (1444) | Filter by category (993) | Workbench „Filter by category“ entspricht dem Tooltip; Legacy „v“ wirkt versehentlich. |
| en | `ui.all.all-entries` | All (7663) | All entries (1735) | Workbench-Text prüfen/übernehmen: vollständiger Filterbegriff; Deutsch muss noch übersetzt werden. |
| en | `ui.all.all-types` | All (7672) | All types (1739) | Workbench-Text „Alle Typen“ / „All types“ passt zum Typfilter. |
| en | `ui.states.all-statuses` | All Statuses (8293) | All statuses (1737) | Nur Groß-/Kleinschreibung; anhand der allgemeinen UI-Schreibweise entscheiden. |

Für `de / ui.all.all-entries` ist weder das unspezifische „Alle“ noch das englische „All entries“ ideal. Vorschlag: **„Alle Einträge“** als ausdrücklich geprüfte neue Übersetzung.

Quellcode-Belege:

- `resources/views/components/ui/button/activate-deactivate.blade.php:24`: beide Sprachaktionen übergeben `language`.
- `resources/views/components/ui/table/pagination.blade.php:50`: Text verbindet erste und letzte Trefferzahl.
- `resources/views/components/admin/partials/permission-list/⚡filter.blade.php:80` und `role-list/⚡filter.blade.php:42`: Kategorie-Filter-Tooltip.
- `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:159` und `:470`: Alle-Einträge-/Typfilter.

### Die 509 Statusunterschiede

| Legacy | Workbench | Textvergleich | Paare |
|---|---|---|---:|
| missing | active | different | 200 |
| missing | obsolete | different | 43 |
| obsolete | active | identical | 56 |
| ok | obsolete | different | 16 |
| ok | obsolete | identical | 194 |

Die 412 Fälle mit leerem Legacy-Wert sind keine zwei konkurrierenden Texte. Ihre Statusverteilung:

| Legacy | Workbench | Paare |
|---|---|---:|
| missing | active | 200 |
| missing | obsolete | 43 |
| obsolete | obsolete | 153 |
| ok | obsolete | 16 |

Insbesondere 194 textgleiche `ok → obsolete` und 56 textgleiche `obsolete → active` benötigen die zugehörige Finding-/Review-Historie. Aus Textgleichheit oder Zeitstempeln allein folgt keine korrekte Übernahmerichtung.

### Mehrfachzeilen und Base-Duplicates

- 102 Paare enthalten jeweils zwei Legacy-Zeilen; davon stehen 81 genau einer Workbench-Zeile gegenüber und 21 keiner.
- 92 Paare entstehen beim Vergleich durch Locale-Schreibweisen mit unterschiedlicher Groß-/Kleinschreibung. Bei 10 Paaren sind auch die originalen Locale-Strings gleich. Die Ursache auf Key-/Datensatzebene ist noch separat zu prüfen.
- 81 Doppelpaare enthalten denselben Text. Die übrigen 21 enthalten einen Text und null; diese betreffen ausschließlich die drei History-Keys in sieben deutschen Sprachvarianten.
- Für diese drei Keys gibt es **21 nichtleere ok-Base-Duplicates** und zusätzlich **21 leere missing-Zeilen mit gesetztem Base-Duplicate-Flag**. Die Flags sind keine ausreichende Grundlage zum Löschen oder Exportieren.
- Die Basissprache de enthält für die drei Keys in beiden Beständen identisch „Ereignis“, „Ereignisse“ und „Historie“.
- Drei weitere Base-Duplicate-Zeilen sind obsolete und gehören zu `admin.permissions.actions.edit_role_permissions` (de-at/de-ch/de-it).

Der Legacy-Exporter schließt `is_base_duplicate=true` ausdrücklich aus (`TranslationsExportLangFiles::translationValuesForLocale`). Ein entsprechendes Foundation-Freigabe-/Fallback-Konzept muss vor dem Abschalten der Legacy-Pflege feststehen. Eine korrekte Runtime-Fallback-Auflösung wurde durch diesen Vergleich nicht bewiesen.

### Reihenfolge für die Bereinigung

1. Die zehn konkreten Textentscheidungen fachlich freigeben; insbesondere den neuen deutschen Text „Alle Einträge“ und Platzhalter berücksichtigen.
2. Statuskonflikte anhand der Finding-/Review-Historie erklären; nichts pauschal reaktivieren oder obsolete setzen.
3. Locale-/Key-Doppelzuordnungen und Base-Duplicate-Policy klären.
4. Erst danach Schreibwege delegieren und Exportfähigkeit einschließlich Sprachdateien/Fallback testen.

### Aktuelle Detaildaten

- JSON-Snapshot: `storage/app/private/translation-value-review-20261013-151924/comparison.json`
- Nach Prüffällen gruppierte Liste: `storage/app/private/translation-value-review-20261013-151924/review.json`
- SHA-256 des Snapshots: `03b3f9caf32e21a9d6834730cac897524403d7b6cd35cebef6488a33336abdf1`

Die privaten Detaildaten enthalten vollständige Werte und IDs. Es wurden keine Übersetzungswerte, Status, Reviews oder Sprachdateien verändert.

## Finding-/Review-Historie der Statusunterschiede

Rein lesende PostgreSQL-Momentaufnahme vom 13.10.2026, 15:41 Uhr. Untersucht wurden die 509 eindeutigen Key-/Locale-Paare mit unterschiedlichem Status. Die Erhebung lief in einer `REPEATABLE READ, READ ONLY`-Transaktion mit anschließendem Rollback.

### Was sich belegen lässt

253 dieser Workbench-Werte sind `obsolete`, 256 sind `active`. Für die 253 obsoleten Werte ergibt die **direkt über `context.lang_value_id` zugeordnete** Historie:

| Befund | Paare | Aussage |
|---|---:|---|
| Letztes direktes Statusereignis: `lang_value_obsoleted` | 235 | Import meldete den Wert als nicht mehr vorhanden. Davon Legacy `ok`: 194, Legacy `missing`: 41. |
| Letztes direktes Statusereignis: `lang_value_changed` mit `obsolete` | 4 | Zusätzlich ist jeweils eine explizite Source-Obsolete-Review vorhanden. |
| Kein direktes Ereignis, das den aktuellen Obsolete-Status erklärt | 14 | Weitere Zuordnungen über Reviews, Key-Änderungen oder Sammelereignisse erforderlich. |

Die 14 verbleibenden Werte haben in ihren aktuellen Metadaten achtmal `translation_key_rekey_target_exists`, zweimal `replaced_by_reviewed_translation_key_ui_button_delete` und viermal keinen Obsolete-Grund. Die vier ohne Grund betreffen `dynamic.ui.button.show_hide.hide_label` und `dynamic.ui.button.show_hide.show_label`, jeweils `de` und `en`. Ein Metadaten-Grund ist ein Hinweis, aber noch kein vollständig rekonstruierter Review-Verlauf.

Die vier expliziten Source-Reviews sind 87, 88, 93 und 94; alle betreffen `en` und wurden am 23.07.2026 gespeichert. Ihre Keys sind `admin.country_reference_list.table.address_format_available`, `admin.country_reference_list.table.phone`, `admin.person_list.filter.filter_by_client` und `admin.user_list.table.overview_of_all_users_in_the_system_their_email_addresses_and_assigned_roles`. Diese Reviews haben keine notwendige Zuordnung zum heutigen Key-Datensatz; eine Suche ausschließlich über `reviews.key_id` übersieht sie.

Der aktuelle Importer erklärt den automatischen Weg: `TranslationWorkbenchLangValueImporter::markMissingValuesObsolete()` setzt aktive, zuvor importierte Werte auf `obsolete`, wenn sie in den eingelesenen Sprachdateien fehlen. Berücksichtigt werden dabei die im Import vertretenen Locales und `meta.source=translation-workbench:import-lang-values`. **Das bedeutet nicht automatisch „bewusst fachlich verworfen“.** `hasObsoleteReviewDecision()` berücksichtigt daneben explizite Source-Obsolete-Reviews nach Locale und Translation-Key.

### Aktive Findings trotz obsoletem Wert

Bei acht Paaren bestehen weiterhin als `active` gespeicherte Finding-Verknüpfungen:

| Key | Locales | Befund |
|---|---|---|
| `admin.app_settings.table_icon_registry.status` | de, en | Wert obsolete; aktive Findings gespeichert. |
| `admin.roles.actions.create` | de, en | Wert obsolete; aktive Findings gespeichert. |
| `admin.translation_list.modal.edit` | de, en | Wert obsolete; aktive Findings gespeichert. |
| `ui.save` | de, en | Zusätzlich Rekey-Hinweis auf `ui.button.save.save`. |

Das sind konkrete Prüffälle, **kein Beweis für aktuell defekte Aufrufe**: Die Findings wurden hier nicht durch einen neuen Scan oder einen Abgleich der heutigen Fundstellen aktualisiert. Für `ui.save` muss insbesondere geprüft werden, ob die Verknüpfungen noch vor der Umbenennung liegen.

Von den 256 aktiven Workbench-Werten haben 254 mindestens ein aktives gespeichertes Finding: alle 200 mit Legacy `missing` und 54 der 56 mit Legacy `obsolete`. Bei den übrigen zwei sind nur andere Finding-Zustände verknüpft. Die direkte Werthistorie reicht auch hier nicht für eine pauschale Freigabe: 191 haben kein direkt zugeordnetes Ereignis mit einem `status`-Feld, bei 36 lautet das letzte solche Ereignis noch `obsolete`, und 29 wurden direkt als `active` entdeckt. Andere Schreibwege/Sammelereignisse müssen zur Erklärung des aktuellen Zustands mit ausgewertet werden.

### Grenzen und nächster gezielter Schritt

- Ein `lang_value_changed` mit ausschließlich `source_hash`/`meta` ist keine Statusänderung und darf nicht als deren Beleg verwendet werden.
- Direkt verknüpfte Timeline-Ereignisse decken nicht zwingend Rekey-, Modal- und Sammelereignisse ab. Fehlende direkte Ereignisse bedeuten deshalb nicht automatisch fehlende Historie.
- Legacy-Audit-Ereignisse wurden für die betroffenen Key-IDs mit erhoben. Key-/Usage-Statusänderungen dürfen nicht mit dem Status einzelner Locale-Werte gleichgesetzt werden; eine vollständige wertbezogene Zuordnung steht noch aus.
- Zuerst die acht Paare mit aktiven Findings gegen die heutigen Fundstellen und Rekey-Ziele prüfen. Danach die 14 nicht direkt erklärten Obsolete-Werte und die indirekten Schreibereignisse der aktiven Werte rekonstruieren. Erst daraus einen konkreten Übernahmeplan ableiten.

Detaildaten: `storage/app/private/translation-status-history-20261013-154110/history.json`; verdichtete Prüfliste: `status-summary.json` im selben Verzeichnis; Erhebungsskript: `audit.php`. Die Summen 509 = 253 + 256 und die acht priorisierten Paare wurden automatisiert geprüft. Anwendungscode, Datenbankbestand und Sprachdateien wurden in diesem Schritt nicht verändert; daher kein erneuter Anwendungstestlauf.

## Abgleich der acht priorisierten Paare mit dem aktuellen Quellcode

Stand 13.10.2026, 15:56 Uhr. Die heutigen Aufrufe wurden einschließlich ihrer Blade-Kommentare geprüft. Zusätzlich wurde der Laravel-Translator im gebooteten CLI-Anwendungskontext für `de` und `en`, jeweils mit und ohne Fallback, aufgerufen. Der konfigurierte Loader ist `Illuminate\Translation\FileLoader`, die Fallback-Sprache `en`. Die aktuellen Workbench-Werte wurden separat in einer rein lesenden Datenbanktransaktion abgeglichen.

| Key | Aktive Fundstelle | Ergebnis de / en | Workbench |
|---|---|---|---|
| `admin.app_settings.table_icon_registry.status` | `resources/views/components/admin/partials/translation-usage-audit/modal/⚡translation-values.blade.php:231` | Status / Status | beide obsolete |
| `admin.roles.actions.create` | `resources/views/components/ui/button/create.blade.php:13`, Default des label-Props | Erstellen / Create | beide obsolete |
| `admin.translation_list.modal.edit` | `resources/views/components/ui/button/edit.blade.php:13`, Default des label-Props | Bearbeiten / Edit | beide obsolete |
| `ui.save` | `resources/views/livewire/admin/pages/settings/⚡profile.blade.php:244`, explizites Button-Label | ui.save / ui.save; Key fehlt auch mit Fallback | beide obsolete |

Die Beispiele in den Kommentaren der Create-/Edit-Components sind zusätzliche Texttreffer, aber nicht die Ursache der aktiven Verwendung: Der jeweilige Prop-Default ist ausführbarer Code. Auf der Profilseite steht ein zweiter `ui.save`-Treffer in einem auskommentierten Button; nur der Aufruf in Zeile 244 ist aktiv. Die vier gespeicherten aktiven Findings (4392, 4770, 4773, 5093; zuletzt gesehen am 07.08.2026) haben damit weiterhin entsprechende aktive Quellcode-Aufrufe. Es handelt sich nicht lediglich um veraltete Finding-Verknüpfungen.

### Sechs Statusabweichungen ohne aktuelle Auflösungslücke

Die ersten drei Keys existieren für beide Locales und werden **ohne Fallback** korrekt aufgelöst. Ihre Texte stimmen mit den obsoleten Workbench-Werten überein. Somit sind die frühere Import-Abwesenheit und der heutige Dateistand auseinanderzuhalten: Die historische Obsolete-Markierung beschreibt nicht zuverlässig die aktuelle Verfügbarkeit. Wann und durch welchen Schreibweg die Dateien wieder befüllt wurden, ist mit dieser Prüfung noch nicht belegt. Auch eine Freigabe zum automatischen Reaktivieren folgt daraus nicht.

### Ein belegter unvollständiger Rekey im Profil-Aufruf

`ui.save` fehlt dagegen in beiden Locales. Das in den Metadaten ausgewiesene Ziel `ui.button.save.save` ist vorhanden und liefert `Speichern` / `Save`; auch die Ziel-Werte 671 (`de`) und 1475 (`en`) sind aktuell `active`. Die gemeinsame Save-Component verwendet dieses Ziel bereits als Default (`resources/views/components/ui/button/save.blade.php:13`). Der Profil-Aufruf überschreibt diesen korrekten Default noch ausdrücklich mit dem alten Key.

Review 1246 vom 06.08.2026 dokumentiert `ui.save` → `ui.button.save.save` für einen Workbench-Modal-Kontext. Zusammen mit den Wert-Metadaten belegt das das vorhandene Rekey-Ziel, aber keine vollständige damalige Umschreibung aller Aufrufstellen. Der heutige Profil-Aufruf ist ein konkreter verbliebener Altverweis.

**Konkreter Korrekturvorschlag:** Den überflüssigen `label`-Override des aktiven Profil-Save-Buttons entfernen, damit der vorhandene gemeinsame Default greift. Den auskommentierten Button bei einer solchen Korrektur ebenfalls auf den Ziel-Key bringen. `ui.save` nicht als Alias wieder einführen und die alten Werte nicht pauschal reaktivieren. Diese Änderung ist in dieser Bestandsaufnahme noch nicht ausgeführt.

### Konsequenz für den gemeinsamen Workflow

Eine belastbare Prüfliste muss die folgenden Zustände getrennt darstellen: aktuelle Code-Verwendung, aktuelle Sprachdatei-/Translator-Auflösung, Workbench-Wertstatus und explizite Review-/Rekey-Entscheidung. Der Vergleich der beiden Datenbankbestände allein konnte diese sechs verfügbaren Übersetzungen und den tatsächlich fehlenden Profil-Key nicht unterscheiden.

Als nächstes sind die verbleibenden 14 nicht direkt erklärten Obsolete-Werte mit ihren Sammel-/Rekey-Ereignissen zu prüfen. Der konkrete Profil-Fix kann separat umgesetzt werden; ein erneuter Import oder eine Pipeline wurde nicht gestartet.

Detaildaten und reproduzierbares Skript: `storage/app/private/translation-eight-pairs-20261013-155635/runtime.json` und `audit.php`. Zwölf Translator-Prüfungen umfassen die acht betroffenen Paare, das Save-Rekey-Ziel sowie `ui.state.status` als Vergleich. Kein Browser-/HTTP-Test; das Ergebnis belegt die Translator-Auflösung im aktuellen CLI-Anwendungskontext. Nur dieser Bericht und private Audit-Dateien wurden ergänzt.

### Profil-Korrektur umgesetzt

Im anschließenden Korrekturschritt wurde der veraltete `label`-Override am aktiven Profil-Save-Button entfernt. Dadurch greift der bestehende Default der gemeinsamen Save-Component. Der alte Key im auskommentierten Flux-Button wurde ebenfalls durch `ui.button.save.save` ersetzt.

Die Save-Component wurde mit einem separaten temporären Blade-Cache in beiden Locales gerendert: `de` ergibt „Speichern“, `en` ergibt „Save“. Die Profil-Datei enthält keinen `__('ui.save')`-Aufruf mehr. Kein vollständiger Profil-/HTTP-Test; keine Datenbank-, Review- oder Sprachdateiänderung. Die sechs übrigen Statusabweichungen bleiben unverändert zur weiteren Prüfung offen.

## Rekonstruktion der 14 verbleibenden Obsolete-Werte

Rein lesende Erhebung vom 13.10.2026, 16:05 Uhr. Zusätzlich zu direkten Wert-Ereignissen wurden `old_values`, `new_values` und `context` nach den sieben betroffenen Keys durchsucht und die darin referenzierten Reviews ausgewertet. Das ergab 591 Timeline-Treffer und neun referenzierte Reviews. Die Suche ist auf diese Keys begrenzt; ein fehlender Treffer beweist keine grundsätzlich fehlende Historie.

| Alte Keys, jeweils de/en | Paare | Beleg für die Stilllegung |
|---|---:|---|
| `admin.permissions.modals.edit.title` | 2 | Event 128263: `obsoleted` enthält IDs 238/1040; Review 1239 und Event 128262 dokumentieren das Ziel `ui.title.title`. |
| `admin.permissions.overview.title` | 2 | Event 128265: IDs 253/1055; Review 1240 und Event 128264 dokumentieren ebenfalls `ui.title.title`. |
| `ui.button.cancel.cancel` | 2 | Event 119243: IDs 676/1480; Review 1013 dokumentiert `ui.cancel`. |
| `ui.save` | 2 | Event 130584: IDs 1554/1555; Review 1246 dokumentiert `ui.button.save.save`. |
| `dynamic.ui.button.show_hide.hide_label` | 2 | Cleanup-Review 949, Entscheidung `obsolete`; Event 113355 enthält beide Werte in `new_values.lang_values`. |
| `dynamic.ui.button.show_hide.show_label` | 2 | Cleanup-Review 950, Entscheidung `obsolete`; Event 113356 enthält beide Werte. |
| `ui.button.delete.delete` | 2 | Metadaten nennen eine Ersetzung durch `ui.button.delete`; die konkrete Statusänderung am 26.07.2026 um 12:57:03 bleibt in dieser Erhebung unbelegt. |

Damit sind **12 der 14 Stilllegungen durch konkrete Ereignisse erklärt**. Für die beiden Delete-Werte gibt es zwar Review 64 (`ui.button.delete.delete` → `ui.delete`, 23.07.2026), aber dieses andere Ziel und Datum erklären die spätere Statusänderung nicht hinreichend. Findings vom 26.07.2026 zeigen anschließend Aufrufe von `ui.button.delete`; das ist ein zusätzlicher Verwendungshinweis, kein Ersatz für den fehlenden Statusbeleg.

### Historische Rekey-Ziele sind keine aktuellen Reparaturvorgaben

Die damaligen Ziel-IDs wurden auch nach ID abgefragt. Ihre heutigen Keys unterscheiden sich teilweise von den historischen Zielnamen:

| Historischer Zielname | Damalige Ziel-IDs de/en | Heutiger Key dieser IDs | Heutiger DB-Status |
|---|---|---|---|
| `ui.title.title` | 221 / 1023 | `ui.title.filter` | active |
| `ui.cancel` | 1694 / 1693 | `ui.button.cancel` | active |
| `ui.button.save.save` | 671 / 1475 | unverändert | active |

Die historischen Namen `ui.title.title` und `ui.cancel` haben außerdem andere, heute obsolete Wertzeilen. Ein Abgleich nur über den Namen würde daher unterschiedliche Entwicklungsstände vermischen. Die Folgerekeys selbst sind hier noch nicht vollständig rekonstruiert.

Der aktuelle Translator-Abgleich für de/en zeigt:

- Die beiden alten Permission-Title-Keys fehlen; auch `ui.title.title` fehlt. Exakte Suchtreffer der alten Keys im Anwendungs-/Package-Code betreffen derzeit Dataset-Auswahlbeschreibungen, keine gefundenen direkten Übersetzungsaufrufe. Dynamische Aufrufe wurden dadurch nicht ausgeschlossen.
- Die beiden bewusst stillgelegten dynamischen Show/Hide-Keys sind weiterhin in Sprachdateien vorhanden und liefern Ausblenden/Hide beziehungsweise Anzeigen/Show. Explizite Obsolete-Entscheidung und Dateiverfügbarkeit sind somit getrennte Zustände. Die aktuelle Show/Hide-Component verwendet andere Keys (`ui.button.show-hide.show`/`hide`).
- `ui.button.cancel.cancel` wird weiterhin aktiv verwendet und korrekt als Abbrechen/Cancel aufgelöst; das historische Ziel `ui.cancel` fehlt.
- `ui.button.delete.delete` wird im Default der Delete-Component korrekt als Löschen/Delete aufgelöst. Das metadatenbasierte Ziel `ui.button.delete` liefert heute dagegen ein **Array**, obwohl die Workbench dafür aktive skalare Werte führt. Eine blinde Ersetzung wäre falsch und könnte erneut einen String-/Array-Fehler auslösen.
- Für Save bleibt der bereits korrigierte Profil-Aufruf der konkrete belegte Altverweis; das Ziel liefert Speichern/Save.

### Folgerung und verbleibende Prüfung

Die Obsolete-Historie rechtfertigt weder pauschale Reaktivierung noch blindes Nachziehen alter Rekey-Zielnamen. Vor Übernahme/Export müssen aktuelle Code-Verwendung, Datensatz-ID samt Folgerekeys, skalare oder strukturierte Dateiknoten und Review-Entscheidungen gemeinsam geprüft werden. Besonders `ui.button.delete` und `ui.button.cancel` sind auf mögliche Skalar-/Array-Konflikte zu untersuchen; bei den Title-Rekeys ist zusätzlich der fachliche Kontext zu prüfen (ursprünglich „Berechtigung bearbeiten“/„Übersicht“, heute Zielwert „Filter“).

Offen bleiben der konkrete Schreibnachweis für die zwei Delete-Obsolete-Werte sowie die Folgerekeys und aktuelle Exportverträglichkeit der Ziele. Es wurden keine Datenbank-, Sprachdatei- oder Anwendungscodeänderungen vorgenommen. Detaildaten und Skript: `storage/app/private/translation-obsolete-fourteen-20261013-160500/audit.json` und `audit.php`. Der Translator wurde im CLI-Anwendungskontext geprüft, nicht per Browser.

## Folgerekeys und konkretes Export-Risiko

Stand 13.10.2026, 16:07 Uhr. Der Foundation-Exporter wurde für `de`/`en`, Namespace `ui`, ausschließlich mit `write=false` innerhalb einer PostgreSQL-Read-only-Transaktion aufgerufen. Keine Pipeline, kein schreibender Export und keine neue Review-Entscheidung wurden ausgeführt.

### Cancel und Delete: aktive Dateistruktur gegen skalare Workbench-Werte

Der aktuelle Translator liefert für `ui.button.cancel` und `ui.button.delete` jeweils Arrays. Die darin enthaltenen skalaren Unterkeys `ui.button.cancel.cancel` und `ui.button.delete.delete` werden von den gemeinsamen Button-Components weiterhin verwendet. In der Workbench sind dagegen die Elternpfade aktive skalare Werte, die Unterkeys obsolete.

Der tatsächliche Exportplan bestätigt das Risiko:

| Kennzahl für de/en/ui | Anzahl |
|---|---:|
| Exportierbare Werte | 452 |
| Neue Werte | 344 |
| Geänderte Werte | 13 |
| Unveränderte Werte | 95 |
| Zum Entfernen vorgesehene Werte | 16 |
| Gemeldete Konflikte | 0 |
| Geschriebene Dateien / Timeline-Ereignisse | 0 / 0 |

In beiden Locales enthält der Plan die Entfernung von `ui.button.cancel.cancel` und `ui.button.delete.delete`. Anschließend plant er, `button.cancel` beziehungsweise `button.delete` als skalaren Text zu setzen. Die Änderungsliste zeigt ausdrücklich Array → String. Damit würden die heute verwendeten Unterkeys verloren gehen. Es handelt sich nicht nur um ein theoretisches Risiko aus einer Code-Lektüre.

Wichtig für eine spätere Korrektur: `TranslationWorkbenchLangFileExporter::planFile()` führt das Pruning **vor** dem Setzen aktiver Werte aus. Ein Schutz, der nur nach dem Pruning auf vorhandene Arrays prüft, reicht deshalb nicht. Zusätzlich prüft `canSetNested()` nur die Elternsegmente eines Zielpfads, nicht das Überschreiben eines Arrays am Ziel selbst. Ein Konfliktschutz muss die ursprüngliche Dateistruktur, geplante Löschungen und Ersetzungen zusammen betrachten. Eine bewusst freigegebene Strukturänderung braucht einen getrennten, nachvollziehbaren Entscheidungsweg; ein obsoleter Datenbankstatus allein belegt keine unbenutzte Code-Fundstelle.

### Cancel-Folgerekey belegt

Auf Review 1013 (`ui.button.cancel.cancel` → `ui.cancel`) folgt Review **1014** (`ui.cancel` → `ui.button.cancel`). Event **119491** vom 03.08.2026, 23:00:20 migriert dabei dieselben Wert-IDs 1693/1694 auf den neuen Elternpfad. Die heutige Diskrepanz zwischen Dateistruktur und Workbench-Werten ist damit nachvollziehbar; sie ist nicht durch einen fehlenden Fallback zu lösen.

### Title-Folgerekeys und fachliche Vermischung

Für die Wert-IDs 221/1023 ist diese Kette belegt:

1. Event 128261: `admin.permissions.filters.title` → `ui.title.title`.
2. Events 128263/128265: Die Werte für „Berechtigung bearbeiten“ und „Übersicht“ werden zugunsten dieses bereits bestehenden Ziels obsolete; ihre Texte werden dabei nicht in den Zielwert übernommen.
3. Review 1242 / Event **128677**: `ui.title.title` → `ui.title.overview`.
4. Review 1243 / Event **129008**: `ui.title.overview` → `ui.title.filter`.

Heute ruft beispielsweise `resources/views/components/admin/partials/permission-list/⚡modal-edit-permission.blade.php:11` im Bearbeitungsmodal `ui.title.filter` auf. Auch `resources/views/components/admin/partials/app-settings/⚡meta-overview.blade.php:9` verwendet diesen Key als Übersichtstitel. Die Sprachdateien enthalten zugleich getrennte `ui.title.filter` und `ui.title.overview`. Das ist eine fachliche Vermischung verschiedener Überschriften; technisch erfolgreiche Auflösung macht diese Zusammenführung nicht automatisch korrekt. Die Herkunft ist durch Review-/Timeline-Ereignisse belegt, eine pauschale Rückumbenennung ist daraus nicht abzuleiten.

### Nächste Korrekturpriorität

Zuerst den Export gegen nicht ausdrücklich freigegebene Strukturverluste absichern: Array → Skalar, einschließlich vorheriger Entfernung seiner Unterkeys, muss als nachvollziehbarer Konflikt erscheinen und das Schreiben der betroffenen Datei verhindern. Das mit isolierten Regressionstests für Dry-Run und Schreibmodus sowie unveränderte Geschwister-Keys absichern. Danach fachliche Entscheidungen zu den kollidierenden Button-Keys und Title-Kontexten treffen. Keine automatische Reaktivierung, kein Alias und kein pauschales Pruning als Abkürzung.

Private Belege: `storage/app/private/translation-rekey-export-20261013-160733/audit.json` und `audit.php`. Der Bericht enthält 923 passende Timeline-Ereignisse, 28 referenzierte Reviews und den vollständigen Dry-Run-Plan. Anwendungscode, produktive Sprachdateien und Datenbankbestand blieben unverändert.

### Export-Schutz umgesetzt und geprüft

Im anschließenden Implementierungsschritt prüft `TranslationWorkbenchLangFileExporter::planFile()` vor dem Setzen eines Wertes jetzt sowohl den ursprünglichen Dateiknoten als auch den geplanten Stand. Eine Array→Skalar-Ersetzung wird als `array_to_scalar_conflict` mit Zielpfad, ursprünglichem Array-Typ und betroffenen Unterkeys gemeldet. Dadurch bleibt die Prüfung wirksam, wenn das vorgeschaltete Pruning bereits sämtliche Unterkeys aus dem geplanten Stand entfernt hat. Wie bei bestehenden Exportkonflikten wird die gesamte betroffene Datei nicht geschrieben; andere konfliktfreie Dateien unterliegen weiterhin dem bisherigen Exportverhalten.

Es gibt keinen neuen Override oder automatischen Freigabeweg. Dieser Schutz löst die fachlichen Status-/Rekey-Konflikte nicht selbst und ist kein genereller Schutz gegen jede isolierte Löschung: Er verhindert die hier untersuchten Strukturverluste bei Array→Skalar-Ersetzungen.

Fünf neue isolierte Regressionstests in `TranslationWorkflowTest.php` prüfen Dry-Run und Schreibmodus jeweils mit/ohne vorgeschaltetes Child-Pruning, unveränderte Dateiinhalte einschließlich anderer geplanter Änderungen, unveränderte Wertzeilen und ausbleibende Timeline-Schreibereignisse. Ein weiterer Testfall innerhalb dieser fünf bestätigt, dass ein normales skalares Blatt weiterhin geändert werden kann und sein Geschwister-Key erhalten bleibt. Gesamtergebnis: **195 Tests, 1010 Assertions, erfolgreich**.

Erneuter realer Dry-Run vom 13.10.2026, 16:11 Uhr: **vier Konflikte**, exakt `ui.button.cancel` und `ui.button.delete` in de/en; **null geschriebene Dateien, null Timeline-Ereignisse**. Beleg: `storage/app/private/translation-rekey-export-20261013-161132/audit.json`. Produktive Übersetzungswerte, Reviews und Sprachdateien wurden nicht verändert. Als nächstes bleiben die fachlichen Entscheidungen zu diesen Keys und den getrennten Title-Kontexten.

## Konkrete fachliche Zuordnung für Buttons und Überschriften

Bestandsabgleich vom 13.10.2026, 16:16 Uhr. Die folgende Zuordnung ist der vorbereitete Korrekturplan, noch keine ausgeführte Datenbank- oder Quellcodeänderung.

| Bedeutung | Vorgesehener skalarer Key | de / en | Begründung |
|---|---|---|---|
| Abbrechen | `ui.button.cancel.cancel` | Abbrechen / Cancel | Aktueller Component-Default und direkte Modal-Aufrufe; vorhandenes skalares Blatt in den Sprachdateien. |
| Löschen | `ui.button.delete.delete` | Löschen / Delete | Aktueller Component-Default; vorhandenes skalares Blatt. |
| Filterbereich | `ui.title.filter` | Filter / Filter | Nur für tatsächliche Filtereingaben verwenden. |
| Übersicht / Zusammenfassung | `ui.title.overview` | Übersicht / Overview | Bereits in Sprachdateien und aktiven Workbench-Werten vorhanden; auch bereits an korrekten Aufrufen verwendet. |
| Berechtigung bearbeiten | `admin.permissions.modals.edit.title` | Berechtigung bearbeiten / Edit Permission | Eigenständige fachliche Aussage; alte Texte sind in Workbench-Werten erhalten. Kein Filter-/Overview-Alias. |

`ui.button.cancel` und `ui.button.delete` bleiben in den Sprachdateien Container. Ihre aktiven skalaren Workbench-Werte sollten nach überprüfter Zusammenführung nicht mehr exportierbar sein. Das ist keine neue Namenskonvention, sondern die Angleichung an die bereits verwendeten Blatt-Keys. Es werden keine Kompatibilitätsaliase vorgeschlagen.

### Betroffene Wertzeilen und Key-Datensätze

- Cancel: Blattwerte 676 (de) / 1480 (en), beide obsolete, stimmen textlich mit den aktiven Elternwerten 1694 (de) / 1693 (en) überein. Blatt-Key 3721 ist obsolete/pending; Eltern-Key-Datensätze 699 und 3560 sind open/reviewed. Eine Korrektur muss auch die Findings/Key-Verknüpfungen dieser Datensätze berücksichtigen, nicht nur vier Wertstatus ändern.
- Delete: Blattwerte 679 (de) / 1483 (en), beide obsolete, stimmen mit Elternwerten 1606 (de) / 1605 (en) überein. Eltern-Key 3566 ist open/reviewed. Für den exakten Blatt-Key wurde in der aktuellen Abfrage kein Workbench-Key-Datensatz gefunden; dessen Zuordnung muss ausdrücklich aufgebaut werden.
- Overview: aktive Werte 2105 (de) / 2106 (en), Key 222 open/reviewed. Zusätzlich existieren obsolete Key-Datensätze unter demselben Namen. Diese werden nicht pauschal reaktiviert oder gelöscht.
- Filter: aktive Werte 221 (de) / 1023 (en), Key 1385 open/reviewed. Diese Werte bleiben Filterwerte.
- Berechtigungsmodal: vorhandene obsolete Werte 238 (de) / 1040 (en); für den exakten alten Key wurde kein heutiger Key-Datensatz gefunden. Vor Wiederverwendung sind Key-/Finding-Zuordnung und Veröffentlichung der erhaltenen Texte erforderlich. Eine alleinige Änderung des Blade-Aufrufs würde aktuell nur den fehlenden Key anzeigen.

### Konkrete Quellcode-Zuordnung

Die echten Filter-Sections behalten `ui.title.filter`. Für die folgenden Übersichtsbereiche ist stattdessen `ui.title.overview` vorgesehen:

- `resources/views/components/admin/partials/app-settings/⚡meta-overview.blade.php`
- `resources/views/components/admin/partials/permission-list/⚡meta-overview.blade.php`
- `resources/views/components/admin/partials/country-reference-list/⚡meta.blade.php`
- `resources/views/components/admin/partials/html-view-audit/⚡meta.blade.php`
- `resources/views/components/admin/partials/fallback-report-list/⚡meta.blade.php`
- `resources/views/components/admin/partials/user-list/⚡meta.blade.php`
- `resources/views/components/admin/partials/client-list/⚡meta.blade.php`
- `resources/views/components/admin/partials/person-list/⚡meta.blade.php`
- `resources/views/components/admin/partials/flag-reference-list/⚡meta.blade.php`
- `resources/views/components/management/people/person-overview/⚡meta.blade.php`
- `resources/views/layouts/app/sidebar/⚡management.blade.php`: Link zur Personenübersicht, kein Filter-Link.
- `resources/views/components/admin/partials/html-view-audit/⚡modal.blade.php`: Separator vor ID/Status und den übrigen Finding-Metadaten; dort sind keine Filtereingaben.

Separat: `resources/views/components/admin/partials/permission-list/⚡modal-edit-permission.blade.php` erhält den spezifischen Bearbeitungstitel, sobald die zugehörige Übersetzung wieder konsistent bereitsteht. Bereits korrekte Overview-Aufrufe (beispielsweise Role-Meta und Workbench-Overview) bleiben unberührt.

### Sichere Reihenfolge der Umsetzung

1. Die zwölf eindeutig falschen Filter→Overview-Aufrufe auf den bereits funktionierenden Overview-Key umstellen. Das benötigt keine produktive Datenänderung.
2. Für Cancel/Delete einen eng begrenzten, überprüfbaren Abgleich vorbereiten: aktuelle Werte und Key-/Finding-Zuordnungen als Vorbedingungen, Blatt als Ziel, Eltern nicht länger als exportierbare Skalare; bestehende Historie behalten und die neue Entscheidung dokumentieren. Keine allgemeinen Import-/Pruning-Kommandos als Ersatz.
3. Den spezifischen Bearbeitungstitel mit seinen vorhandenen Texten konsistent wieder bereitstellen und erst dann den Modal-Aufruf umstellen.
4. Dry-Run erneut auf erwartete Änderungen prüfen; ein tatsächlicher Export darf nicht die übrigen 344 neuen Werte beziehungsweise die weiteren Pruning-Vorschläge dieses bisherigen UI-Plans unbeabsichtigt mit veröffentlichen.

Die geplante Zuordnung vermeidet sowohl neue Aliase als auch eine globale Umbenennung von `ui.title.filter`. Private Momentaufnahme: `storage/app/private/translation-key-decisions-20261013-161634/snapshot.json`; Erhebung: `audit.php`. In diesem Schritt wurden nur Dokumentation und private Audit-Dateien ergänzt; keine produktiven Daten, Reviews, Sprachdateien oder Aufrufe verändert.

### Zwölf Overview-Aufrufe korrigiert

Die oben einzeln aufgeführten zwölf Views verwenden jetzt `ui.title.overview` statt `ui.title.filter`. Pro Datei wurde ausschließlich dieser eine Key-Aufruf geändert. Die neun tatsächlichen Filter-Views behalten ihren Filter-Key; das Berechtigungs-Bearbeitungsmodal bleibt für die gesonderte Wiederherstellung seines spezifischen Titels offen.

Prüfung: Alle zwölf vorgesehenen Ersetzungen und die neun unveränderten Filter-Aufrufe wurden kontrolliert. Der gebootete Laravel-Translator liefert ohne Fallback `Übersicht` (de) / `Overview` (en) sowie unverändert `Filter` in beiden Sprachen. Whitespace-Prüfung erfolgreich; kein vollständiger Browser-/HTTP-Test. Keine Datenbank-, Review- oder Sprachdateiänderungen.

### Gezielten Export als Voraussetzung für den Datenabgleich ergänzt

Der bestehende Foundation-Exporter und `translation-workbench:export-lang-files` unterstützen jetzt eine exakte Key-Auswahl. Beispiel für einen Dry-Run:

```bash
php artisan translation-workbench:export-lang-files --locale=de --locale=en --key=ui.button.cancel --key=ui.button.delete
```

Die wiederholbare Option `--key` begrenzt sowohl exportierte Werte als auch Pruning-Kandidaten und die betroffenen Dateipläne. Ohne diese Option bleibt das bisherige Verhalten erhalten. Keys werden exakt verglichen, nicht als Präfix oder Wildcard; eine explizit leere Auswahlzeichenfolge wird abgewiesen, damit sie keinen Voll-Export auslöst. Ein unbekannter Key ergibt keinen Dateiplan. Die Key-Auswahl wird im JSON-Bericht, in der Command-Ausgabe und im Workbench-Exportbericht angezeigt. Wie bisher schreibt der CLI-Dry-Run einen lokalen Report, aber keine Sprachdateien oder Timeline-Ereignisse.

Der Array→Skalar-Schutz bleibt auch bei begrenztem Export aktiv. Das ist keine Freigabe für die noch konfligierenden Button-Werte. Explizit ausgewählte obsolete Werte können weiterhin zum Pruning vorgesehen werden; die Option ersetzt keine fachliche Prüfung.

Realer rein lesender Service-Dry-Run vom 13.10.2026, 16:55 Uhr für die beiden Eltern-Keys: genau vier ausgewählte Werte und vier Strukturkonflikte, null neue/geänderte Werte, null Pruning-Kandidaten, null geschriebene Dateien und Timeline-Ereignisse. Private Belege: `storage/app/private/translation-selected-export-20261013-165538/audit.json` und `audit.php`. Der gemeinsame UI-Report wurde durch diese Erhebung nicht überschrieben.

Isolierte Tests sichern Dry-Run/Schreibmodus, unveränderte nicht ausgewählte Werte einschließlich obsoleter Geschwister, unberührte andere Namespace-Dateien, exakte statt Präfix-Auswahl, unbekannte/leere Keys sowie Command- und Report-Weitergabe ab. Die eigentliche Zusammenführung von Cancel/Delete und die Wiederherstellung des spezifischen Modal-Titels bleiben der nächste Schritt; in diesem Vorbereitungsschritt wurden keine produktiven Werte, Reviews, Sprachdateien oder Modal-Aufrufe geändert.

### Geprüfte Wiederherstellung von Cancel/Delete und Modal-Titel abgeschlossen

Am 13.10.2026 wurde der begrenzte Datenabgleich tatsächlich ausgeführt. Die vorherigen Befunde oben bleiben als historische Bestandsaufnahme erhalten; für diese drei Ziele gilt jetzt:

| Ziel | Wiederhergestellte Zuordnung | Werte de/en |
| --- | --- | --- |
| `ui.button.cancel.cancel` | Bestehende Key-Datensätze 699 und 3560, open/reviewed | 676 / 1480 active: Abbrechen / Cancel |
| `ui.button.delete.delete` | Bestehender Key-Datensatz 3566, open/reviewed | 679 / 1483 active: Löschen / Delete |
| `admin.permissions.modals.edit.title` | Historischer Modal-Key 1832, open/reviewed | 238 / 1040 active: Berechtigung bearbeiten / Edit Permission |

Die vier bisherigen skalaren Elternwerte unter `ui.button.cancel` und `ui.button.delete` sind obsolete. Ihre Texte wurden nicht verändert oder gelöscht. Die vorhandenen Blattwerte wurden über den TranslationWriter reaktiviert. Key-Entscheidungen erfolgten über den KeyReviewWriter mit Review-/Timeline-Protokollierung. Der gemeinsame Filter-Key 1385 wurde nicht umbenannt.

Beim Modal war der ursprüngliche Key-Datensatz historisch nach `ui.title.title` umbenannt worden. Ein fokussierter Rescan bewahrt diese bereits geprüfte Entscheidung absichtlich. Zwei abgesicherte Versuche brachen deshalb vor der Veröffentlichung ab; jeweils wurden die Datenbanktransaktion zurückgerollt und die Modal-Datei wiederhergestellt. Der erfolgreiche Lauf prüfte anschließend ausdrücklich, dass Key 1832 keine anderen aktiven Finding-Zuordnungen und der historische Name keine aktiven Werte hatte, bevor er die konkrete Modal-Zuordnung wiederherstellte. Dieses Verhalten wird durch einen eigenen Regressionstest abgesichert.

Die zehn betroffenen Quelldateien wurden ausschließlich für die drei Ziel-Keys synchronisiert, ohne globales Obsolete-Markieren oder Truncate. Überholte aktive Findings/Verknüpfungen wurden eng auf die gesicherten alten Ausdrücke begrenzt stillgelegt. Es wurden keine neuen Key-Datensätze erstellt; der fokussierte Sync meldete sechs neue und vier aktualisierte Findings.

Dateiänderungen dieser Wiederherstellung:

- `resources/views/components/admin/partials/permission-list/⚡modal-edit-permission.blade.php`: spezifischer Modal-Titel statt Filter-Titel.
- `lang/de/admin.php` und `lang/en/admin.php`: jeweils genau ein zusätzlicher Übersetzungswert für den Modal-Titel. Der bestehende Exporter normalisiert dabei auch die Leerzeilenformatierung dieser generierten Dateien. Alle übrigen Werte wurden gegen die Sicherung auf Gleichheit geprüft.
- `lang/de/ui.php` und `lang/en/ui.php`: bytegleich zur Sicherung; die Button-Blattwerte waren dort bereits vorhanden.
- `tests/Unit/TranslationWorkbench/TranslationWorkflowTest.php`: gezielte Regressionen für Button-Recovery, separaten Modal-Titel und explizite Wiederherstellung einer historisch geprüften Umbenennung.

Sicherung und Nachweise: `storage/app/private/translation-reviewed-recovery-20261013-171751/`. Dort liegen `before.json` einschließlich Datei-Inhalten und Ausgangszuordnungen, der tatsächlich ausgeführte Lauf `repair-applied.php`, `publication-plan.json`, `applied.json` sowie `verified.json` und die lesende Prüfung `verify.php`. Der erfolgreiche Export schrieb zwei Dateien, ohne Konflikte oder Pruning. Ein anschließender rein lesender Exportplan für genau diese fünf Ziel-/Eltern-Keys meldet null neue/geänderte/entfernte Werte, null Konflikte und null geschriebene Dateien.

Der gebootete Laravel-Translator liefert alle drei korrigierten Übersetzungen sowie unverändert Filter und Overview in de/en ohne Fallback. Die isolierte Workbench-Suite besteht mit **202 Tests und 1054 Assertions**. Die Whitespace-Prüfung der hier betroffenen Dateien besteht; die allgemeine Prüfung meldet vorhandene, davon unabhängige Probleme in `_ide_helper.php`. Eine vollständige Browserprüfung wurde in diesem Schritt nicht durchgeführt. Der übrige Datenbestand wurde nicht pauschal abgeglichen oder exportiert.

### Browserprüfung und zwei weitere Veröffentlichungslücken geschlossen

Die anschließende Browserprüfung mit dem Testzugang öffnete `/admin/permissions` und ein bestehendes Berechtigungsmodal, ohne Formulardaten zu speichern. HTTP 200, keine gemeldeten JavaScript-Ausnahmen; „Berechtigung bearbeiten“, „Abbrechen“ und „Speichern“ wurden korrekt angezeigt. Dabei erschienen jedoch zwei weitere Beschriftungen als rohe Keys. Der erneute rein lesende Datenabgleich belegte:

| Key | Aktive Workbench-Werte de / en | Befund |
| --- | --- | --- |
| `ui.assign.assigned.assigned-roles` | Zugewiesene Rollen / Assigned roles | Geprüfter offener Key; beide Werte fehlen in den Sprachdateien. |
| `ui.sort.sort.sort-order` | Sortierreihenfolge / Sort order | Geprüfte offene Key-Zuordnungen; beide Werte fehlen in den Sprachdateien. |

Diese vier vorhandenen Werte wurden über den bestehenden Foundation-Exporter mit exakter Key- und Locale-Auswahl veröffentlicht. Geändert wurden ausschließlich `lang/de/ui.php` und `lang/en/ui.php`; vorhandene Übersetzungen wurden auf Gleichheit geprüft. Der Exporter normalisiert daneben die Formatierung der generierten Dateien. Keine Quellcode-Key-Umbenennung, keine neuen Übersetzungstexte, keine Statusänderungen oder Bereinigung der Legacy-Daten. Vier Export-Timeline-Ereignisse dokumentieren die Veröffentlichung.

Die anschließende Browserprüfung zeigt jetzt auch „Zugewiesene Rollen“ und „Sortierreihenfolge“ korrekt, weiterhin ohne JavaScript-Ausnahmen. Der gezielte Folge-Dry-Run meldet vier unveränderte Werte, keine neuen/geänderten/entfernten Werte und keine Konflikte. PHP-Syntax- und Whitespace-Prüfung beider Sprachdateien erfolgreich. Anwendungscode wurde in diesem Schritt nicht verändert; die zuvor erfolgreiche Suite mit 202 Tests wurde deshalb nicht erneut ausgeführt.

Belege:

- Neuer Bestandsvergleich vor diesen vier Veröffentlichungen: `storage/app/private/translation-value-comparison-20261013-191325/comparison.json`.
- Begrenzter Plan, ursprüngliche Datei-Inhalte, Veröffentlichungsskript und Ergebnis: `storage/app/private/translation-modal-label-export-20261013-192257/`.
- Folge-Dry-Run: `storage/app/private/translation-modal-label-export-20261013-192354/plan.json`.

Der aktualisierte Vergleich enthält nach der vorherigen Recovery noch 503 statt 509 Statusunterschiede; dies bedeutet nicht 503 fehlerhafte Übersetzungen. Vor dieser Veröffentlichung fehlten 456 aktive, nichtleere Workbench-Werte mit nicht-obsoletem Key in ihrer zugehörigen Sprachdatei. Nach den vier gezielt ergänzten Werten verbleiben rechnerisch 452. Der nächste Schritt ist deren gruppierte Exportprüfung einschließlich Strukturkonflikten und Pruning-Folgen, nicht ein ungeprüfter Gesamtexport. Die zehn fachlichen Textunterschiede zwischen Legacy und Workbench sowie Locale-/Base-Duplicate-Fragen bleiben separate Prüfpunkte.

## Gruppierte Exportprüfung der verbleibenden Veröffentlichungen

Rein lesende Momentaufnahme vom 13.10.2026, 19:26 Uhr. Der bestehende Foundation-Exporter wurde einmal ohne Key-Einschränkung und zusätzlich je Locale/Namespace mit exakt den dort fehlenden Keys im Dry-Run ausgeführt. Alle Datenbankabfragen liefen in einer `REPEATABLE READ, READ ONLY`-Transaktion mit Rollback. Sprachdatei-Hashes und Timeline-Ereignisanzahl blieben unverändert; kein gemeinsamer UI-Exportbericht wurde überschrieben.

### Fehlende Werte nach Namespace

Die Zahlen beziehen sich auf **Key-/Locale-Werte**, nicht auf unterschiedliche Keys. „Geprüft“ bedeutet hier ausschließlich: Mindestens eine nicht-obsolete Key-Zuordnung hat `review_status=reviewed`. Das ersetzt weder die Prüfung des Übersetzungstexts noch einen Abgleich mit der heutigen Verwendung.

| Namespace / Locale | Fehlend | Mit geprüfter Key-Zuordnung | Ohne geprüfte Key-Zuordnung |
| --- | ---: | ---: | ---: |
| admin / de | 8 | 2 | 6 |
| admin / en | 7 | 2 | 5 |
| management / de | 2 | 2 | 0 |
| management / en | 2 | 2 | 0 |
| packages / de | 43 | 36 | 7 |
| packages / en | 43 | 36 | 7 |
| ui / de | 170 | 162 | 8 |
| ui / en | 170 | 162 | 8 |
| ui / sieben deutsche Sprachvarianten | 7 | 7 | 0 |
| **Summe** | **452** | **411** | **41** |

Die 411 Werte mit geprüfter Key-Zuordnung betreffen 202 verschiedene Keys; die 41 übrigen Werte betreffen 21 Keys. Die sieben regionalen Werte gehören sämtlich zu `ui.per_page_selector.per_page` mit dem Text „Pro Seite“. Sie werden zur noch offenen Regional-/Fallback-Policy getrennt gehalten.

Zusätzlich fehlen zwei aktive Werte zu `ui.label` (de „Falsche Kennzeichnung“, en „False labeling“), für die kein nicht-obsoleter Workbench-Key existiert. Sie sind **nicht** in den 452 enthalten und werden vom Exporter nicht als exportierbar eingeordnet. Ihre Status-/Rekey-Zuordnung benötigt eine separate Prüfung.

### Vollständiger und gezielter Export haben unterschiedliche Folgen

| Plan | Neue Werte | Geänderte Texte | Löschvorschläge | Strukturkonflikte | Geschriebene Dateien |
| --- | ---: | ---: | ---: | ---: | ---: |
| Vollständiger Dry-Run, 54 Dateipläne | 452 | 10 | 215 | 0 | 0 |
| Nur fehlende Keys, nach Locale/Namespace begrenzt | 452 | 0 | 0 | 0 | 0 |

Die zehn Textänderungen entsprechen den zuvor dokumentierten zehn nichtleeren Textunterschieden: sechs deutsche ui-Werte, drei englische ui-Werte und der englische Kategorie-Filtertext. Insbesondere würde der Voll-Export `de / ui.all.all-entries` von „Alle“ auf das englische „All entries“ ändern. Die Abwesenheit von Strukturkonflikten bedeutet daher ausdrücklich nicht, dass der Gesamtplan fachlich korrekt ist.

Alle 215 Löschvorschläge tragen den Grund `obsolete_lang_value`: admin 166, ui 19, pages 16, dynamic 4, layouts 4 sowie account, laravel_translation_workbench und management jeweils 2. Die Zahlen umfassen alle im Plan enthaltenen Locales. Der Vorschlag ergibt sich aus dem gespeicherten Wertstatus und belegt nicht, dass der jeweilige heutige Quellcode den Eintrag nicht mehr benötigt. Es wurde nichts entfernt.

### Konkrete weitere Reihenfolge

1. Die **404 de/en-Werte mit geprüfter Key-Zuordnung** gegen aktuelle aktive Quellcode-Verwendungen prüfen und in überschaubaren Namespace-Gruppen gezielt veröffentlichen. Die exakten Key-Filter vermeiden die zehn Textänderungen und die 215 Löschvorschläge.
2. Die **41 Werte ohne geprüfte Key-Zuordnung** anhand ihrer Findings prüfen. Der aktuelle Exporter fordert einen aktiven Wert und einen nicht-obsoleten Key, aber keine abgeschlossene Key-Prüfung; daher sind diese Werte technisch im Plan enthalten. Diese bestehende Regel wurde nicht stillschweigend geändert.
3. Die sieben regionalen Werte und die zwei Werte ohne aktiven Key separat klären.
4. Über die zehn Textänderungen konkret entscheiden und die 215 Löschvorschläge erst nach Prüfung heutiger Verwendungen behandeln. Kein Voll-Export als Abkürzung.

Private Detaildaten: `storage/app/private/translation-unpublished-audit-20261013-192631/audit.json` mit vollständigem und begrenzten Plänen, `audit.php` zur Reproduktion sowie `reviewed-candidates.json` und `pending-candidates.json` mit den konkreten Kandidaten. In diesem Schritt wurde ausschließlich geprüft und dokumentiert; Anwendungscode, Übersetzungswerte, Reviews und Sprachdateien blieben unverändert. Deshalb kein erneuter Anwendungstestlauf.

## Geprüfte Werte mit aktuellen Quellcode-Aufrufen veröffentlicht

Am 13.10.2026 wurde die angekündigte Prüfung der 404 de/en-Kandidaten durchgeführt. Der vorhandene `TranslationWorkbenchScanner` untersuchte 1634 Dateien innerhalb seiner konfigurierten Pfade und Ausschlussregeln. Berücksichtigt wurden exakte `translationKey`-Treffer außerhalb von `source_context=commented_out`; alle erfassten Treffer hatten den Scanner-Typ `key`. Es wurde keine Foundation-Synchronisierung oder vollständige Pipeline gestartet.

Für 360 Key-/Locale-Werte ließ sich mindestens ein aktueller Aufruf nachweisen. 44 Werte wurden zurückgestellt. Ein fehlender statischer Scanner-Treffer beweist keine fehlende dynamische Verwendung; deshalb wurden weder deren Status geändert noch Werte gelöscht.

| Namespace | Veröffentlichte Werte de/en zusammen | Dateien |
| --- | ---: | --- |
| admin | 4 | `lang/de/admin.php`, `lang/en/admin.php` |
| management | 2 | `lang/de/management.php`, `lang/en/management.php` |
| packages | 62 | `lang/de/packages.php`, `lang/en/packages.php` |
| ui | 292 | `lang/de/ui.php`, `lang/en/ui.php` |
| **Summe** | **360** | **8 Sprachdateien** |

Vor dem Schreiben wurden Quellcode-Hashes, Sprachdatei-Inhalte, aktive Werte, geprüfte Key-Zuordnungen und die vollständigen begrenzten Exportpläne erneut überprüft. Die Veröffentlichung verwendete den bestehenden Foundation-Exporter mit exakter Key-/Locale-/Namespace-Auswahl, einer Datenbanktransaktion und Dateisicherung für einen Abbruchfall. Ergebnis: 360 neue Werte, keine Änderung vorhandener Übersetzungstexte, kein Pruning, keine Strukturkonflikte; 360 Export-Timeline-Ereignisse. Die übrigen Werte jeder Datei wurden gegen die gesicherte Fassung auf Gleichheit geprüft. Die normale Formatierung generierter Sprachdateien erfolgt weiterhin durch den Exporter.

Die vorhandenen Workbench-Texte wurden unverändert übernommen. „Geprüfte Key-Zuordnung und heutige Verwendung“ ist keine Aussage über die sprachliche Qualität jeder Übersetzung. Eine redaktionelle Prüfung oder Änderung dieser Texte wurde nicht vorgenommen.

Verifikation: Alle 360 Werte werden vom gebooteten Laravel-Translator in ihrer konkreten Sprache ohne Fallback aufgelöst. Die acht gezielten Folge-Exportpläne haben null Änderungen, Löschvorschläge oder Konflikte. PHP-Syntax- und Whitespace-Prüfung der acht Dateien erfolgreich. Die isolierte Workbench-Suite besteht erneut mit **202 Tests / 1054 Assertions**. Kein vollständiger Browserdurchlauf sämtlicher betroffener Oberflächen.

Private Nachweise: `storage/app/private/translation-reviewed-publication-20261013-193152/` mit `plan.json` (Kandidaten, zurückgestellte Werte, Quellcode-Fundstellen und Hashes, Datei-Sicherungen, Exportpläne), `run.php`, `applied.json`, `verified.json` und `verify.php`.

Von den ursprünglich 452 fehlenden Werten bleiben damit **92** zurück: 44 ohne aktuell nachgewiesenen exakten Scanner-Aufruf, 41 ohne geprüfte Key-Zuordnung und sieben regionale Werte. Hinzu kommen weiterhin separat die beiden aktiven Werte ohne nicht-obsoleten Key, die zehn Textentscheidungen und die 215 Löschvorschläge des vollständigen Exportplans. Als nächstes werden die 44 zurückgestellten Werte auf dynamische Verwendung, umgezogene Aufrufe und historische Umbenennungen eingegrenzt.

## Die 44 zurückgestellten Werte: Quellcode und Historie eingegrenzt

Rein lesende Prüfung vom 13.10.2026. Die 44 de/en-Werte gehören zu 22 Keys. Ausgewertet wurden 24 historische Key-Datensätze, 59 Findings samt Dateipfaden und 51 Reviews. Anschließend wurden die betroffenen Dateien erneut mit dem bestehenden Scanner gelesen und heutige Ausdrücke über Pfad plus Ausdruck, Source-Signature oder Fingerprint zugeordnet. Eine zusätzliche exakte Textsuche prüfte Ressourcen, Anwendungscode, Packages, Konfiguration und Routen.

| Heutiger Befund | Keys | Werte de/en | Konsequenz |
| --- | ---: | ---: | --- |
| Aktive Literale statt geprüfter Keys | 16 | 32 | Quellcode-Update und Veröffentlichung gemeinsam vorbereiten; Export allein ändert diese Aufrufe nicht. |
| Frühere Keys im aktiven Aufruf | 2 | 4 | Individuelle Rekey-Prüfung; bestehende funktionierende Auflösung berücksichtigen. |
| Ausschließlich auskommentierte Testbeispiele | 3 | 6 | Kein aktueller Veröffentlichungsbedarf für diese Beispiele; nicht löschen oder reaktivieren. |
| Dynamisch erzeugte Dokumentkategorie | 1 | 2 | Runtime-Optionen und Dynamic-Multi-Zuordnung prüfen; keinen festen Kategorie-Key in den Ausdruck einsetzen. |
| **Summe** | **22** | **44** | |

### Zwei frühere Key-Namen

- Die aktiven Rollen-Modal-Aufrufe in `resources/views/components/admin/partials/role-list/⚡modal-create.blade.php:71` und `⚡modal-edit.blade.php:93` verwenden `ui.assignable-through-ui`. Der geprüfte Workbench-Key heißt `ui.assignable.assignable-through-ui`; Review 1113 dokumentiert die Scalar→Array-Transformation. Beide Namen sind derzeit in de/en nicht auflösbar. Hier besteht eine konkrete Auflösungs- und Quellcode-Update-Lücke. Die gleichlautenden auskommentierten Zeilen bleiben separat zu behandeln.
- `resources/views/components/ui/button/close.blade.php:14` verwendet noch `ui.actions.close`. Dieser Aufruf liefert bereits „Schließen“ / „Close“. Der geprüfte Workbench-Zielkey `ui.button.close.close` fehlt in den Sprachdateien. Das ist eine offene Rekey-Angleichung, aktuell aber keine sichtbare Übersetzungslücke; keinen bestehenden Wert vorsorglich entfernen.

### Besondere Fälle

Die drei Test-Keys `ui.label.label-testing-2`, `ui.literal.literal-testing` und `ui.literal.literal-testing-2` stehen im Blade-kommentierten Testbereich von `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings.blade.php`. Ein bloßer Texttreffer darf hier nicht als aktiver Aufruf gelten.

`management.people.edit_person.sections.documents.archive_modal.category` gehört zum aktiven Ausdruck `__(str($document->category)->headline()->toString())` in `resources/views/components/management/people/edit-person/sections/documents/⚡archive-modal.blade.php:170`. Die Historie enthält ausdrücklich `dynamic_single_confirmed` und anschließend `dynamic_multi_confirmed`. Der skalare Key ist deshalb kein direkter Ersatz für die zur Laufzeit wechselnde Kategorie. Die Optionen-/Wertzuordnung ist separat zu prüfen.

### Einzelne Zuordnungen als Arbeitsliste

Die Literal-Fälle enthalten unter anderem Platzhalter für Passwort und Personen-IDs. Bei einem späteren Quellcode-Update müssen die vorhandenen Ersetzungsargumente vollständig erhalten bleiben. Eine geprüfte Key-Entscheidung bedeutet nicht, dass das dazugehörige Quellcode-Update bereits ausgeführt wurde.

| Key | Einordnung | Aktuelle Fundstelle(n) |
| --- | --- | --- |
| `management.people.edit_person.sections.documents.archive_modal.category` | Dynamische Kategorie | `resources/views/components/management/people/edit-person/sections/documents/⚡archive-modal.blade.php:170` |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.stores_how_this_duplicate_expression_should_be_handled_before_code_updates_are_applied` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/code-update-plan.blade.php:499` |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.table_work_findings.cell_actions.edit_translation_values` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-actions.blade.php:44`<br>`packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit.blade.php:17` |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_code_update_conflict_review.store_the_explicit_decision_for_a_duplicate_expression_before_source_code_updates_are_applied` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-code-update-conflict-review.blade.php:17` |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.inspect_and_reset_persisted_translation_workbench_ui_settings_for_the_current_user_an_a06c4c2605` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/settings.blade.php:6` |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.user_settings_are_stored_on_the_user_record_export_files_are_diagnostic_snapshots_tha_d71eaffe64` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/settings.blade.php:36` |
| `ui.assignable.assignable-through-ui` | Früherer Key im Aufruf | `resources/views/components/admin/partials/role-list/⚡modal-create.blade.php:71`<br>`resources/views/components/admin/partials/role-list/⚡modal-edit.blade.php:93` |
| `ui.badge.created_password.tooltip.temporary_password_password_this_password_is_shown_once_here_and_is_also_written_to` | Aktives Literal | `resources/views/components/ui/badge/created-password.blade.php:8` |
| `ui.badge.created_person.tooltip.person_number_person_number_created_person_id_person_id_created_user_id_user_id` | Aktives Literal | `resources/views/components/ui/badge/created-person.blade.php:10` |
| `ui.button.close.close` | Früherer Key im Aufruf | `resources/views/components/ui/button/close.blade.php:14` |
| `ui.filter.show-legacy-dynamic-entries-that-were-reactivated-from-stale-audit-state` | Aktives Literal | `resources/views/components/admin/partials/translation-list/⚡filter.blade.php:195` |
| `ui.label.label-testing-2` | Nur auskommentierte Tests | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings.blade.php:35`<br>`packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings.blade.php:38` |
| `ui.literal.dynamic.this-finding-is-treated-as-a-dynamic-multi-entry-multiple-runtime-option-values-may-n` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-states.blade.php:75`<br>`packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-states.blade.php:369` |
| `ui.literal.dynamic.this-finding-is-treated-as-dynamic-the-displayed-value-is-resolved-from-runtime-data` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-states.blade.php:82`<br>`packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-states.blade.php:382` |
| `ui.literal.literal-testing` | Nur auskommentierte Tests | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings.blade.php:23`<br>`packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings.blade.php:26` |
| `ui.literal.literal-testing-2` | Nur auskommentierte Tests | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings.blade.php:29`<br>`packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings.blade.php:32` |
| `ui.obsolete.the-translation-key-was-not-found-during-the-latest-audit-scan-and-was-marked-as-obsolete` | Aktives Literal | `resources/views/components/admin/partials/translation-list/modal-history/timeline/stale-marked.blade.php:23` |
| `ui.remove.remove.remove-or-restore-leading-translation-key-segments-while-the-key-has-not-been-manually-edited` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-edit-translation-key.blade.php:589` |
| `ui.remove.removes.removes-the-first-dot-separated-segment-from-the-current-search-text-useful-when-expo` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-filters.blade.php:40` |
| `ui.remove.removes.removes-the-first-dot-separated-segment-from-the-current-value-search-text-useful-when` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-filters.blade.php:582` |
| `ui.remove.removes.removes-the-selected-scalar-candidates-from-the-current-review-selection` | Aktives Literal | `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/scalar-review.blade.php:328` |
| `ui.stale.a-previously-stale-or-obsolete-translation-entry-was-found-again-and-reactivated` | Aktives Literal | `resources/views/components/admin/partials/translation-list/modal-history/timeline.blade.php:90` |

Private Nachweise: `storage/app/private/translation-deferred-audit-20261013-194114/` mit `audit.json`, `current-source.json`, `classification.json` und den reproduzierbaren Erhebungsskripten. Die Runtime-Prüfung der vier alten/neuen Rollen-/Close-Keys erfolgte mit dem gebooteten Laravel-Translator explizit für de/en ohne Fallback. Kein vollständiger Browser-Test dieser Fundstellen.

Nächster Umsetzungsschritt: Den bestehenden Code-Update-Planner für die 16 Literal-Keys und die zwei Rekey-Fälle gezielt auswerten, einschließlich Duplikatentscheidungen und Platzhaltererhalt. Danach die konfliktfreien Aufrufänderungen zusammen mit ihrem exakten de/en-Export umsetzen. Die Testbeispiele und die dynamische Kategorie bleiben dabei außen vor. In dieser Prüfung wurden keine Anwendungssourcen, Sprachdateien, Übersetzungswerte oder Reviews verändert; kein erneuter Anwendungstestlauf erforderlich.

## Literal-/Rekey-Aufrufe korrigiert und veröffentlicht

Der Dry-Run der 18 vorgesehenen Keys zeigte zwei gemeinsame Ursachen im Code-Update: Der Scanner speichert normalisierte Ausdrücke, während Planner/Applier bisher wortwörtliche Teilstrings suchten; mehrzeilige Aufrufe wurden deshalb als stale eingeordnet. Zudem zählte `substr_count` gleichlautende Aufrufe in Kommentaren als Duplikate.

Die Korrektur liegt in den vorhandenen Services:

- `src/Scanner/TranslationWorkbenchScanner.php`: `activeSourceCalls()` ermittelt passende Aufrufe unter Beachtung der bestehenden Kommentarerkennung. PHP-Tokenvergleich ignoriert Formatierungs-Whitespace außerhalb von Strings; Änderungen innerhalb von Stringargumenten werden nicht gleichgesetzt. Die vorhandene normalisierte Scan-/Fingerprint-Darstellung bleibt erhalten.
- `src/Foundation/TranslationWorkbenchCodeUpdatePlanner.php`: verwendet den tatsächlich vorhandenen Aufruf einschließlich Formatierung für die geplante Ersetzung. Eine optionale exakte Key-Auswahl begrenzt den Plan zusätzlich zu den Pfaden.
- `src/Foundation/TranslationWorkbenchCodeUpdateApplier.php`: Zählung, Ersetzung und Vorschau verwenden dieselben aktiven Fundstellen. Kommentare bleiben unverändert. Die bestehende Duplikat-Review-Logik bleibt für mehrere aktive Aufrufe erhalten. Die exakte Key-Auswahl wird an den Planner weitergegeben.

Alle Pfade oben liegen unter `packages/gunreip/laravel-translation-workbench/`. Neue Regressionen in `tests/Unit/TranslationWorkbench/TranslationWorkflowTest.php` prüfen mehrzeilige Aufrufe mit kommentiertem Zwilling, unveränderte Ersatzargumente, unveränderte Literal-Inhalte und die exakte Key-Eingrenzung. Suite: **204 Tests / 1063 Assertions erfolgreich**.

Der anschließend eng begrenzte Plan wurde mit Datei-Sicherung und Datenbanktransaktion ausgeführt: **21 Aufrufstellen in 16 Blade-Dateien**, **36 neue Übersetzungswerte in vier Sprachdateien** (`lang/de/ui.php`, `lang/en/ui.php`, `lang/de/packages.php`, `lang/en/packages.php`). Keine bestehenden Texte verändert, keine Werte entfernt. Der tatsächliche Source-Diff wurde gegen die vollständige Dry-Run-Vorschau auf Gleichheit geprüft. Die beiden Badge-Aufrufe behalten ihre Ersetzungsargumente; Rollenformular und Close-Component verwenden jetzt ihre geprüften Ziel-Keys. Die auskommentierten Tests und die dynamische Dokumentkategorie wurden nicht umgestellt.

Der Applier-Bericht zählt 19 angewendete Finding-Zeilen und zwei anschließend als `stale_source` eingeordnete Zeilen: Die ersten Ersetzungen der jeweils identischen aktiven Duplikatgruppe hatten die weiteren Vorkommen bereits ersetzt. Die Diff-Prüfung und der anschließende Scanner-Abgleich bestätigen alle vorgesehenen Aufrufänderungen. 19 Code-Update-Ereignisse und 36 Export-Ereignisse wurden protokolliert. Die gespeicherten Findings sind nach diesem Applier-Lauf noch nicht neu synchronisiert; dieser gezielte Folgeabgleich bleibt ausdrücklich offen, damit die Arbeitsansicht den neuen Quellstand ebenfalls abbildet.

Verifikation: alle 36 Werte über Laravel in de/en ohne Fallback aufgelöst; alle 17 im begrenzten Dateisatz enthaltenen Blade-Views kompiliert und das resultierende PHP auf Syntax geprüft (16 geändert, eine zusätzliche geprüfte Datei unverändert). Kein vollständiger Browserdurchlauf.

Sicherung und Belege: `storage/app/private/translation-deferred-plan-20261013-195308/` mit `plan.json`, `before-files.json`, `export-plan.json`, `apply.php`, `applied.json` und `verified.json`. Der ursprüngliche blockierte Dry-Run liegt unter `translation-deferred-plan-20261013-195041/`.

Von den 92 zurückgestellten Veröffentlichungen verbleiben nach diesen 36 Ergänzungen **56 Werte**: sechs auskommentierte Testwerte, zwei Werte zur dynamischen Kategorie, 41 Werte ohne geprüfte Key-Zuordnung und sieben regionale Werte. Die getrennten Textentscheidungen, Werte ohne aktive Keys und Löschvorschläge bleiben unverändert offen.

## Gezielter Finding-Abgleich nach dem Quellcode-Update abgeschlossen

Am 13.10.2026 wurden ausschließlich die 21 aktuellen Aufrufe der 18 zuvor korrigierten Keys synchronisiert. Verwendet wurde der bestehende FoundationSyncer ohne Truncate und ohne globales Obsolete-Markieren. Ergebnis: 21 neue Findings mit 21 aktiven Verknüpfungen zu den bestehenden geprüften Keys; keine neuen Keys, keine obsoleten Keys. Die 16 betroffenen Source-File-Datensätze wurden aktualisiert.

Die 21 ursprünglichen Findings aus dem gesicherten Code-Update-Plan wurden einzeln geprüft: Ihre alten Ausdrücke kommen nicht mehr als aktive Aufrufe vor. Nur diese Findings und ihre aktiven Verknüpfungen wurden mit dem bestehenden TimelineRecorder als ersetzt/obsolete protokolliert. Die Historie wurde nicht gelöscht. Die aktuelle Sync-Zusammenfassung enthält 42 Timeline-Ereignisse; die gesonderte Stilllegung der alten Findings und Verknüpfungen wird zusätzlich protokolliert und ist nicht in dieser Sync-Zahl enthalten.

Vor dem Commit wurden folgende Nachbedingungen geprüft:

- Der auf diese Keys und Pfade begrenzte Code-Update-Plan enthält exakt **21 reviewed findings / 21 already current**.
- **Null** safe updates, stale sources, manual reviews, missing values, missing files oder unsupported expressions in diesem Plan.
- Bestehende Übersetzungswertzeilen und Reviews sind vollständig unverändert; Key-Name, Status und Review-Entscheidung einschließlich Reviewer/Zeitpunkt sind unverändert.
- Der gezielte de/en-Exportplan enthält keine Ergänzungen, Änderungen, Löschvorschläge oder Konflikte.
- Die Quellcode-Dateien sind hashgleich zum Beginn des Abgleichs.

Sicherung, ausgeführtes Skript und Ergebnis: `storage/app/private/translation-deferred-resync-20261013-195724/` mit `before.json`, `run.php` und `applied.json`. Der Abgleich lief in einer SERIALIZABLE-Transaktion. Es wurden keine Anwendungscode- oder Sprachdateiänderungen vorgenommen; die zuletzt erfolgreich geprüfte Suite bleibt bei 204 Tests / 1063 Assertions. Die konkrete Datenkorrektur wurde über die genannten Nachbedingungen geprüft, nicht durch einen neuen allgemeinen Testlauf.

Damit ist der Folgeabgleich dieses Pakets abgeschlossen. Als nächste getrennte Gruppe bleiben die **41 Werte ohne geprüfte Key-Zuordnung** zu untersuchen. Die sechs kommentierten Testwerte, zwei dynamischen Kategoriewerte und sieben regionalen Werte bleiben unverändert zurückgestellt.

## Prüfung der 41 Werte mit aktuell ungeprüften Key-Zuordnungen

Rein lesende Erhebung vom 13.10.2026. Die Gruppe umfasst 21 Keys mit 41 de/en-Werten (beim Zwei-Faktor-Hinweis gehört nur der deutsche Wert zu dieser Fehlstellenliste). Untersucht wurden 57 aktuelle/historische Key-Datensätze, 76 Findings und 23 Reviews; die hinterlegten Dateien wurden erneut gescannt.

**18 der 21 Keys besitzen bereits historische geprüfte Datensätze**, die heute obsolete sind. Aktuelle Datensätze desselben Namens sind dagegen open/pending. Der aktuelle Pending-Status bedeutet hier also nicht automatisch „noch nie geprüft“. Diese historischen Entscheidungen sind keine pauschale Erlaubnis, alte Keys zu reaktivieren oder Review-Metadaten auf neue Datensätze zu kopieren.

Der aktuelle `TranslationWorkbenchFoundationSyncer::reviewedKeyForTranslationKey()` berücksichtigt nur offene geprüfte Keys. Das erklärt, warum ein obsoleter geprüfter Datensatz bei einem abweichenden Fingerprint nicht als bestehender geprüfter Ziel-Key wiederverwendet wird. Die Abfrage allein beweist nicht die historische Ursache jeder einzelnen Obsolete-/Neuanlage; dazu müssten die jeweiligen Timeline-Ereignisse herangezogen werden. Die globale Auswahlregel wurde nicht verändert.

### Ausnahmen vor einer Veröffentlichung

- `ui.assignable-through-ui`: Der aktive Rollenformular-Aufruf wurde im vorherigen Schritt bereits auf `ui.assignable.assignable-through-ui` umgestellt. Die alten Treffer sind nur Kommentare. Nicht durch erneute Veröffentlichung des Alt-Keys einen zweiten Weg etablieren.
- DEV-Playground-Key: Keine aktive passende Verwendung in den aktuellen Dateien. Die alte Darstellung liegt unter `resources/views/livewire/old/entries.blade.php` des Packages und verwendet das Literal `DEV-Playground`; die historischen Findings belegen außerdem kommentierte/obsolete Zustände. Keine Veröffentlichung für eine stillgelegte Darstellung.
- `ui.open`: Die aktuellen geprüften Fundstellen verwenden den Begriff als Workflow-/Finding-Status. Der vorhandene deutsche Wert „Öffnen“ ist eine Aktion; hier wäre „Offen“ angemessen. Nicht unverändert veröffentlichen. Vor einer Änderung alle aktiven Verwendungen prüfen, damit eine eventuell existierende Aktion separat bleibt.
- `ui.badge.create_person.person_created`: Der deutsche gespeicherte Wert ist ebenfalls `Person created`. Eine Freigabe des Keys ersetzt die fehlende deutsche Übersetzung nicht; Vorschlag „Person erstellt“.
- `ui.close`: Historischer Key-Review vorhanden, zusätzlich Review 962 mit `needs_literal_restore`. Dieser spätere Review betrifft ausdrücklich Finding 6904 in `entries/modal-resolve-export-conflict.blade.php`, nicht pauschal sämtliche Close-Aufrufe. Heutige andere Aufrufe und das bereits veröffentlichte Ziel `ui.button.close.close` müssen gezielt abgeglichen werden; die Einzelentscheidung darf nicht global übertragen werden.
- `ui.active` und `ui.assigned-users`: aktive Aufrufe, aber kein passender historischer Key-Review in dieser Erhebung. Bei `ui.assigned-users` außerdem die bereits vorhandene Struktur `ui.assign.assigned.assigned-users` vor einer erneuten Alt-Key-Veröffentlichung prüfen.

Auch weitere vorhandene Übersetzungstexte verdienen eine spätere redaktionelle Prüfung, beispielsweise „Antragsfertiger Plan“ für `Apply-ready plan`. In diesem Schritt wurden keine Texte stillschweigend geändert.

### Konkrete Prüfliste

| Key | Fehlende Werte | Historisch geprüfte Key-IDs | Einordnung |
| --- | ---: | --- | --- |
| `admin.pages.settings.two_factor_setup_modal.or_enter_the_code_manually` | 1 | 3768 | Historische Review-Zuordnung prüfen |
| `admin.partials.html_view_audit.table.show_finding_details` | 2 | 1752 | Historische Review-Zuordnung prüfen |
| `admin.partials.person_list.filter.filter_by_client` | 2 | 1905 | Historische Review-Zuordnung prüfen |
| `admin.partials.translation_list.modal_history.timeline.shared.affected_usages.show_usage_locations` | 2 | 2296 | Historische Review-Zuordnung prüfen |
| `admin.partials.user_list.table.overview_of_all_users_in_the_system_their_email_addresses_and_assigned_roles` | 2 | 2950 | Historische Review-Zuordnung prüfen |
| `admin.permission_list.edit_role_permissions` | 2 | 2980 | Historische Review-Zuordnung prüfen |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dev_playground` | 2 | 220 | Nur alte/archivierte DEV-Darstellung |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_ready_plan` | 2 | 4415 | Historische Review-Zuordnung prüfen |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_result_freshness` | 2 | 4494 | Historische Review-Zuordnung prüfen |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_results` | 2 | 4438 | Historische Review-Zuordnung prüfen |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.controlled_write_workflow` | 2 | 4420 | Historische Review-Zuordnung prüfen |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.current_expression` | 2 | 4460 | Historische Review-Zuordnung prüfen |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.duplicates` | 2 | 4433 | Historische Review-Zuordnung prüfen |
| `ui.active` | 2 |  | Keine passende historische Key-Freigabe |
| `ui.assignable-through-ui` | 2 |  | Bereits ersetzter Alt-Key; nur Kommentare |
| `ui.assigned-users` | 2 |  | Keine passende historische Key-Freigabe |
| `ui.badge.create_person.person_created` | 2 | 179 | Historische Review; deutscher Text englisch |
| `ui.badge.created_password.generated_temporary_password` | 2 | 3545 | Historische Review-Zuordnung prüfen |
| `ui.badge.created_password.temporary_password` | 2 | 3547 | Historische Review-Zuordnung prüfen |
| `ui.close` | 2 | 2513 | Alte Review plus separate Provenance-Entscheidung |
| `ui.open` | 2 | 1548 | Status statt Aktion; deutscher Text unpassend |

Private Belege: `storage/app/private/translation-pending-audit-20261013-195854/` mit `audit.json`, `current-source.json`, `classification.json` sowie den Erhebungsskripten. Keine Daten-, Quellcode- oder Sprachdateiänderungen; daher kein neuer Anwendungstestlauf.

Als nächstes die eindeutig weiterhin verwendeten historischen Zuordnungen eng begrenzt rekonstruieren: ursprüngliche geprüfte Key-IDs, aktuelle aktive Findings und alte Relation-Status gegenüberstellen. Dabei Historie erhalten und weder einen neuen Pending-Key automatisch als reviewed markieren noch alte Entscheidungen ohne Nachweis umhängen. Die oben genannten Ausnahmen bleiben getrennt. Die Zahl offener Veröffentlichungen bleibt in diesem Prüfungsschritt unverändert bei 56.

## Historisch geprüfte Zuordnungen gezielt wiederhergestellt

Die Timeline-Prüfung vom 13.10.2026 belegt für die 14 ausgewählten Keys jeweils `key_candidate_obsoleted` mit dem Grund `no_active_findings_after_foundation_sync`, unmittelbar nach dem Stilllegen nicht mehr gefundener alter Findings. Die aktuellen Aufrufe verwenden exakt dieselben Keys, waren aber mit neueren Pending-Datensätzen verbunden. In dieser Gruppe wurde keine gegenteilige spätere Review-Entscheidung festgestellt. Die zuvor genannten Sonderfälle wurden nicht einbezogen.

Der abgesicherte Abgleich stellte die 14 ursprünglichen geprüften Key-Datensätze wieder auf open. Die 15 neueren Pending-Datensätze wurden als ersetzte Zuordnungen obsolete gesetzt und per TimelineRecorder mit dem jeweiligen ursprünglichen Key verknüpft dokumentiert. Der bestehende FoundationSyncer verband die 15 heutigen Findings mit den ursprünglichen geprüften Keys und legte die 15 bisherigen aktiven Pending-Verknüpfungen still. Keine neuen Keys oder Findings, keine gelöschte Historie, kein globaler Scan und keine globale Änderung der Sync-Auswahlregel.

Die ursprünglichen Reviews wurden nicht auf neue Datensätze kopiert oder neu erfunden: Review-Status, Reviewer, Review-Zeitpunkt und Key-Name wurden für alle betroffenen Datensätze auf Unverändertheit geprüft. Auch sämtliche betroffenen Übersetzungswertzeilen und Review-Zeilen blieben unverändert. Nur Key-Status, Finding-Zuordnungen und die zugehörigen Sync-/Timeline-Daten wurden angepasst.

Anschließend wurden **27 fehlende Werte** über den vorhandenen Exporter mit exakter Key-/Locale-Auswahl in sechs Sprachdateien veröffentlicht: `lang/de/admin.php`, `lang/en/admin.php`, `lang/de/packages.php`, `lang/en/packages.php`, `lang/de/ui.php`, `lang/en/ui.php`. Vorhandene Übersetzungstexte blieben unverändert; kein Pruning und keine Strukturkonflikte. Die vorhandenen sprachlichen Formulierungen wurden übernommen, nicht redaktionell überarbeitet.

Nachbedingungen vor dem Commit: exakt 15 aktive Verknüpfungen an den ursprünglichen Keys, keine aktive Verknüpfung an den ersetzten Pending-Keys, genau 15 reviewed findings / already current im begrenzten Code-Update-Plan, unveränderte Quellcode-Hashes und keine Änderungen an anderen Sprachwerten. Der gesamte Abgleich lief in einer SERIALIZABLE-Transaktion mit Dateisicherung und Rücksetzungsmöglichkeit.

Die anschließende unabhängige Prüfung löst alle 27 veröffentlichten Werte in ihrer jeweiligen Sprache über Laravel ohne Fallback auf. Der gezielte Folge-Exportplan enthält null neue/geänderte/entfernte Werte, null Konflikte und null geschriebene Dateien. Sicherung und Nachweise: `storage/app/private/translation-historical-key-plan-20261013-201537/` mit ursprünglichen Datensätzen und Timeline-Ereignissen in `plan.json`, `before-files.json`, `export-plan.json`, ausgeführtem `apply.php`, `applied.json` und `verified.json`/`verify.php`.

Es verbleiben **29 Werte** aus der ursprünglichen Fehlstellenliste: 14 Werte der sieben ausgesparten Pending-/Sonderfall-Keys, sechs kommentierte Testwerte, zwei dynamische Kategoriewerte und sieben regionale Werte. Als nächstes sollten die weiter aktiv benutzten Alt-Keys `ui.active`, `ui.assigned-users`, `ui.close` und der Status `ui.open` fachlich mit den vorhandenen Ziel-Keys abgeglichen werden. Die DEV-Spielwiese und der bereits ersetzte `ui.assignable-through-ui` bleiben davon getrennt.

## Aktive Alt-Keys und Status „Offen“ korrigiert

Die heutige Verwendung der vier ausgewählten Alt-Keys wurde anhand von Quellcode, Findings und den vorhandenen Zielwerten geprüft. Ergebnis und Umsetzung:

| Alter Aufruf | Heutige Zuordnung | Aktive Aufrufstellen |
| --- | --- | ---: |
| `ui.active` | `ui.state.active` – Aktiv / Active | 1 |
| `ui.assigned-users` | `ui.assign.assigned.assigned-users` – Zugewiesene Benutzer / Assigned users | 1 |
| `ui.close` | `ui.button.close.close` – Schließen / Close | 3 |
| `ui.open` | Key bleibt bestehen, Statuswert Offen / Open | 6 |

Alle sechs untersuchten `ui.open`-Aufrufe sind Statusanzeigen: Workflow-Filter, Finding-Statusauswertung oder Cleanup-Entscheidungsstatus. Es wurde kein aktiver Aufruf als Öffnen-Aktion unter diesem exakten Key gefunden. Der deutsche Workbench-Wert wurde deshalb über den TranslationWriter von „Öffnen“ auf „Offen“ korrigiert; die Key-Entscheidung am aktuellen Datensatz 4514 wurde ausdrücklich protokolliert. Andere historische/pending Datensätze desselben Namens wurden nicht pauschal umgeschrieben.

Die fünf anderen Aufrufe wurden über KeyReviewWriter, CodeUpdateApplier und FoundationSyncer auf die bereits geprüften Ziel-Keys geführt. Die sechs alten de/en-Wertzeilen wurden beim Rekey durch den vorhandenen Writer obsolete gesetzt, da die textgleichen Zielwerte bereits aktiv vorhanden waren. Keine Alias-Veröffentlichung, keine Änderung der Zieltexte und kein Löschen der alten Historie. Ersetzte Findings/Verknüpfungen wurden einzeln mit Timeline-Eintrag stillgelegt; ehemalige Übergangs-Key-Datensätze ohne aktive Verknüpfung wurden obsolete gesetzt. Der begrenzte Code-Update-Plan zeigt danach exakt fünf aktuelle Aufrufe.

Die frühere Provenance-Entscheidung `needs_literal_restore` für `ui.close` bezog sich auf das separate, heute obsolete Finding 6904 im Export-Konfliktmodal. Sie wurde weder geändert noch auf die drei aktuellen Aufrufe in anderen Dateien übertragen. Für diese heutigen Aufrufe wurden neue nachvollziehbare Key-Entscheidungen aufgezeichnet.

Geänderte Quellcode-Dateien (je ein aktiver Aufruf, Kommentare unverändert):

- `resources/views/components/admin/partials/country-reference-list/⚡table.blade.php`
- `resources/views/components/admin/partials/role-list/⚡modal-edit.blade.php`
- `resources/views/components/admin/partials/activity-log/⚡modal.blade.php`
- `resources/views/components/admin/partials/translation-list/⚡modal-history.blade.php`
- `resources/views/livewire/admin/pages/settings/⚡two-factor-setup-modal.blade.php`

In `lang/de/ui.php` und `lang/en/ui.php` wurde ausschließlich der fehlende Statuswert `ui.open` ergänzt. Vorhandene Übersetzungstexte wurden gegen die gesicherte Fassung auf Gleichheit geprüft. Der tatsächliche Code-Diff entspricht dem vor dem Schreiben erzeugten begrenzten Plan. Der gesamte Abgleich lief transaktional mit Datei-Sicherung und Rücksetzungsmöglichkeit.

Verifikation: alle vier verwendeten Keys lösen in de/en ohne Fallback die erwarteten Texte auf; die fünf geänderten Blade-Views wurden kompiliert und auf PHP-Syntax geprüft. Die isolierte Workbench-Suite besteht mit **204 Tests / 1063 Assertions**. Kein vollständiger Browserdurchlauf. Nachweise und Sicherung: `storage/app/private/translation-alias-audit-20261013-202657/` mit `audit.json`, `before-files.json`, ausgeführtem `apply.php`, `write-plan.json`, `applied.json` und `verified.json`.

Aus der vorherigen Liste von 29 Werten sind acht geklärt: sechs Alt-Key-Werte wurden durch bereits veröffentlichte Ziele ersetzt, zwei Statuswerte veröffentlicht. **21 Werte bleiben zurückgestellt**: zwei DEV-Spielwiesenwerte, zwei bereits ersetzte Assignable-Altwerte, zwei Person-created-Werte (darunter englischer deutscher Text), sechs kommentierte Testwerte, zwei dynamische Kategoriewerte und sieben regionale Werte. Diese wurden nicht nebenbei verändert.

## „Person angelegt“ aus der freigegebenen Historie wiederhergestellt

Der aktuelle Key `ui.badge.create_person.person_created` wird an drei aktiven Stellen verwendet: zweimal im Badge `resources/views/components/ui/badge/created-person.blade.php` und einmal als Erfolgsmeldung in `app/Livewire/Management/People/CreatePerson.php`. Seine ursprüngliche Zuordnung ist Key 179, freigegeben durch Reviews 77/78. Die spätere Duplicate-Entscheidung 105 bestätigt dieselbe Key-Verwendung. Der ursprüngliche Key wurde durch Foundation-Sync wegen `no_active_findings_after_foundation_sync` obsolete gesetzt; seine aktuellen Aufrufe landeten an den Pending-Keys 4551 und 4612.

Die Timeline enthält zudem die bereits manuell gepflegte deutsche Übersetzung **„Person angelegt“**, zuletzt in Ereignis 90019, zuvor auch in 49787. Der heutige Wert „Person created“ widerspricht dieser dokumentierten Korrektur. Deshalb wurde die ursprüngliche Übersetzung wiederhergestellt, statt die zunächst vorgeschlagene neue Formulierung „Person erstellt“ einzuführen. Englisch bleibt „Person created“. Die Untersuchung belegt die Abweichung; welcher spätere Import den deutschen Wert wieder ersetzte, ist damit noch nicht abschließend nachgewiesen.

Gesicherte Umsetzung in einer SERIALIZABLE-Transaktion: Key 179 reaktiviert, beide Pending-Zuordnungen stillgelegt, die drei aktuellen Findings über den bestehenden FoundationSyncer mit Key 179 verbunden. Die fünf bisherigen aktiven Pending-Verknüpfungen wurden obsolete; keine neuen Keys oder Findings und keine Änderung der ursprünglichen Reviews. Der deutsche Wert 1575 wurde über den TranslationWriter korrigiert und mit einem neuen Timeline-Eintrag dokumentiert. Der englische Wert 1574 blieb unverändert.

Anschließend wurden ausschließlich die zwei fehlenden de/en-Werte dieses Keys über den bestehenden Exporter in `lang/de/ui.php` und `lang/en/ui.php` veröffentlicht. Keine Quellcodeänderung, keine Änderung anderer Sprachwerte, kein Pruning. Die drei Aufrufe sind im begrenzten Code-Update-Plan bereits aktuell. Unabhängige Nachprüfung: beide Texte werden durch Laravel ohne Fallback korrekt aufgelöst, die Badge-View kompiliert syntaktisch korrekt, der Folge-Exportplan ist leer.

Sicherung und Nachweise: `storage/app/private/translation-person-created-plan-20261013-203337/` mit Daten-/Historiensnapshot, Sprachdateisicherung, ausgeführtem Skript, Exportplan sowie `applied.json` und `verified.json`. Aus der ursprünglichen Fehlstellenliste verbleiben **19 zurückgestellte Werte**: zwei DEV-Spielwiesenwerte, zwei bereits ersetzte Assignable-Altwerte, sechs kommentierte Testwerte, zwei dynamische Kategoriewerte und sieben regionale Werte. Diese Gruppen bleiben getrennt zu prüfen.

## Regionale „Pro Seite“-Werte veröffentlicht; dynamische Kategorien abgegrenzt

Die sieben aktiven regionalen Werte für `ui.per_page_selector.per_page` gehören zum geprüften Key 2456. Die aktuelle Quellcodeprüfung fand dessen drei aktive Aufrufstellen. Für de-at, de-be, de-ch, de-de, de-it, de-li und de-lu fehlte jeweils der neue Key in der Sprachdatei, obwohl dort noch der frühere `ui.table.per_page_selector.per_page` vorhanden war. Die vorhandenen Locale-Bezeichner und Alt-Key-Einträge wurden in diesem Schritt nicht verändert.

Über den vorhandenen Exporter wurden exakt sieben neue Werte „Pro Seite“ in den sieben regionalen `lang/<locale>/ui.php` veröffentlicht. SERIALIZABLE-Transaktion, Dateisicherung, Prüfung unveränderter Quelldateien/Werte/Freigabe und Abgleich aller anderen Sprachwerte sichern den begrenzten Schreibumfang. Keine neuen Key-Entscheidungen, keine Übersetzungsänderungen und kein Pruning. Die unabhängige Laravel-Auflösung ohne Fallback besteht für alle sieben Werte; alle sieben Folge-Exportpläne sind leer. Da kein Anwendungscode geändert wurde, wurde kein weiterer vollständiger Pest-Lauf benötigt.

Nachweise: `storage/app/private/translation-regional-publication-20261013-203757/` enthält Plan und Dateisicherung, ausgeführtes Skript, `applied.json`, `verified.json` und die separate Kategorieprüfung. Der gemeinsame historische Snapshot liegt unter `storage/app/private/translation-regional-category-plan-20261013-203703/`.

Der andere geprüfte Key `management.people.edit_person.sections.documents.archive_modal.category` (3214) wurde ausdrücklich als dynamisch und mehrwertig freigegeben (Reviews 9 und 19). Finding 2686 verwendet weiterhin `__(str($document->category)->headline()->toString())`. Es übersetzt einen konkreten Kategorienamen; die gespeicherten skalaren Werte „Kategorie/category“ sind kein Ersatz dafür. Sie wurden deshalb weder veröffentlicht noch anstelle des dynamischen Ausdrucks eingesetzt.

Die vorhandene `TranslationWorkbenchDynamicOptions::rows(3214, 2686, ['source' => 'en', 'active' => 'de'])` liefert aktuell keine Optionen. Die sieben im Modell definierten Kategorien identity, insurance, tax, residence, correspondence, medical und other werden über den heutigen Ausdruck in de weiterhin als englische Headline aufgelöst. `EditPerson::collectRuntimeDynamicOptions()` besitzt bereits einen Collector-Aufruf für `management.people.edit_person.sections.documents.archive.category_options`; damit ist die Erfassung grundsätzlich vorgesehen, eine funktionierende Zuordnung/Übersetzung für dieses Finding aber durch die Prüfung nicht belegt. Als nächstes ist dieser dynamische Weg gezielt von Collector über Quellenzuordnung und Editor bis zur Laufzeitauflösung zu prüfen. Kein Personen- oder Dokumentdatensatz wurde verändert.

Von der ursprünglichen Liste verbleiben **12 zurückgestellte Werte**: zwei DEV-Spielwiesenwerte, zwei bereits ersetzte Assignable-Altwerte, sechs kommentierte Testwerte sowie zwei skalare Kategorie-Werte. Diese Zahl beschreibt die ursprüngliche Fehlstellenliste, nicht sämtliche noch ungeklärten dynamischen Übersetzungen.

## Dynamischer Kategorie-Workflow: zwei getrennte Unterbrechungen nachgewiesen

Die lesende Bestandsaufnahme vom 13.10.2026 verfolgt Collector, Quellenzuordnung, Editor und Anzeige. Sicherung der untersuchten Daten: `storage/app/private/translation-dynamic-category-20261013-204047/audit.json`. Der Collector ist zur Laufzeitkonfiguration aktiviert und wird aus `EditPerson` aufgerufen.

| Stufe | Nachgewiesener Zustand |
| --- | --- |
| Kategorie-Optionen der Dokumentansicht | Laufzeitquelle 31, Key 3267, aktiv; acht Optionen einschließlich `documentation` |
| Archiv-Kategorie-Optionen | Laufzeitquelle 48, Key 3776, obsolete; sieben gespeicherte Standardoptionen weiterhin vorhanden |
| Archiv-Finding | Finding 2686, geprüfter dynamischer Key 3214, Quelle 53 mit Scope `category`, needs_review |
| Quellenverknüpfung | Keine bestätigte related_dynamic_source-Verknüpfung für Archiv-Key 3214/Finding 2686 |
| Bereits gepflegte Übersetzungen | Je acht Kategorien × de/en an Key 3267 und Key 3188: insgesamt 32 gespeicherte dynamische Wertzeilen |
| Editor des Archivs | Keine Optionen, weil weder eigene Werte noch eine gültige zugeordnete Laufzeitquelle verfügbar sind |
| Veröffentlichung | `TranslationWorkbenchLangFileExporter` liest `TranslationWorkbenchLangValue`, nicht `translation_workbench_dynamic_key_values` |
| Anzeige | Archiv verwendet weiterhin `__(str($document->category)->headline()->toString())`, also z. B. `__('Identity')` |

Die vorhandenen deutschen Kategorie-Texte sind nicht verloren: Korrespondenz, Dokumentation, Identität, Versicherung, Medizin, Sonstiges, Wohnort und Steuern sind im dynamischen Bestand gespeichert. In dieser Prüfung wurde kein Text redaktionell geändert oder auf einen anderen Key kopiert.

Die automatische Quellenauflösung in `ResolveUnknownDynamicSources::bestRuntimeSourceForSource()` verlangt identische Scopes. `category`, `document_archive_category_options` und `document_category_options` sind unterschiedlich; bloßes Wiederholen des Resolvers würde diese Zuordnung daher nicht herstellen. Die bestätigten Verknüpfungen der Dokumentansicht und des Hinzufügen-Dialogs bleiben davon unberührt. Der genaue historische Anlass für den obsolete-Status der Archivquelle ist mit dem vorliegenden Snapshot nicht belegt.

Unabhängig davon fehlt im untersuchten aktuellen Veröffentlichungspfad die Verbindung zwischen dynamisch gespeicherten Optionswerten und Laravel-Anzeige. `TranslationWorkbenchTranslationWriter::saveDynamicValues()` schreibt dynamische Werte und Timeline, aber keine Sprachdateien. Die geprüften Leser dieser Tabelle dienen Workbench-Editor, Inventar und Diagnose; ein Anwendungs-Resolver bzw. Export dieser Werte für den heutigen Archiv-Aufruf wurde nicht gefunden. Die direkte Laravel-Prüfung des vorherigen Schritts bestätigt die englischen Headline-Rückgaben in de.

Der nächste Umsetzungsschritt sollte daher einen expliziten gemeinsamen Vertrag für dynamische Optionen einführen: geprüfte Quellzuordnung → stabiler Options-Key → gezielter Veröffentlichungsvorschlag → Aufruf dieses Keys mit dem konkreten Optionswert. Vorhandene Kategorie-Texte bleiben die Grundlage. Kein automatischer Export der nativen Labels als globale JSON-Keys, keine pauschale Ersetzung der konkreten Kategorie durch „Kategorie“, keine stillschweigende Zusammenführung verschiedener Optionsmengen. Veröffentlichung und Quellcodeanpassung müssen im selben überprüfbaren Plan zusammenpassen; Konflikte zwischen skalarem Eltern-Key und Options-Unterkeys müssen sichtbar bleiben.

Dieser Schritt war eine Diagnose: keine Änderungen an Anwendungslogik, Produktionsdaten, Freigaben oder Sprachdateien.

## Dynamische Optionen durchgängig veröffentlicht und Kategorien angeschlossen

Der bestehende Exporter und `translation-workbench:export-lang-files` unterstützen jetzt den ausdrücklich ausgewählten Modus `--dynamic-options`. Er verlangt mindestens einen konkreten Besitzer-Key. Der normale skalare Export bleibt unverändert. Beispiel für einen zunächst schreibfreien Plan:

```bash
php artisan translation-workbench:export-lang-files \
  --dynamic-options \
  --key=management.people.edit_person.sections.documents.document_category_options \
  --locale=de --locale=en
```

Erst `--write` veröffentlicht den Plan. Dieser Modus liest aktive, einzeln geprüfte Werte aus `translation_workbench_dynamic_key_values`. Der ausgewählte Besitzer muss eindeutig, nicht obsolete, reviewed und dynamisch klassifiziert sein; eine explizite Ablehnung dynamischer Kandidaten blockiert ihn. Es gibt kein automatisches Pruning dynamischer Werte. Fehlende oder ungeprüfte Werte werden nicht als fertige Übersetzungen veröffentlicht.

`TranslationWorkbenchOption::key()` bildet den stabilen Datei-Key `<Besitzer>.options.<Optionswert>`. Der Optionswert wird als genau ein Segment URL-kodiert, einschließlich separat kodierter Punkte. Dadurch sind etwa `a.b` und `a%2Eb` verschieden und erzeugen keine unbeabsichtigte Array-Hierarchie. Der bestehende Exporter prüft weiterhin strukturelle Konflikte und verweigert die komplette betroffene Datei, wenn ein skalarer Elternwert im Weg steht. Exportereignisse werden am Besitzer-Key aufgezeichnet; der konkrete Optionswert ist im Kontext enthalten.

`TranslationWorkbenchOption::label()` liest ausschließlich die veröffentlichten Laravel-Sprachdateien, ohne Workbench-Datenbankabfrage beim Rendern. Fehlende Übersetzungen behalten die übergebene lesbare Originalbezeichnung. Der Scanner erkennt den expliziten vollständig qualifizierten Optionsaufruf als dynamic_multi mit dem im Code angegebenen Besitzer-Key. Kommentare bleiben als solche erkannt; der gemeinsame geprüfte Key bleibt auch beim Rescan erhalten.

Konkrete Umsetzung für Dokumentkategorien: Die Dokumentansicht, der Hinzufügen-Dialog und das Archiv verwenden nun den bestehenden Besitzer-Key 3267 `management.people.edit_person.sections.documents.document_category_options`. Die drei Aufrufe stehen in:

- `resources/views/components/management/people/edit-person/sections/⚡documents.blade.php`
- `resources/views/components/management/people/edit-person/sections/documents/⚡add-modal.blade.php`
- `resources/views/components/management/people/edit-person/sections/documents/⚡archive-modal.blade.php`

Die acht bereits gepflegten Kategorien wurden in de/en veröffentlicht: **16 neue Werte** in `lang/de/management.php` und `lang/en/management.php`. Keine neuen Formulierungen und keine Änderungen der Optionslisten oder Personen-/Dokumentdaten. Die bisherige separate Archiv-Quelle wurde nicht pauschal reaktiviert: Die Anzeige benennt nun ausdrücklich den gemeinsamen Besitzer, dessen aktive Laufzeitquelle 31 bereits vorhanden ist. Frühere separate Übersetzungsdatensätze bleiben historisch erhalten und wurden nicht stillschweigend gelöscht oder zusammengeführt.

Die gesicherte Umsetzung mit SERIALIZABLE-Transaktion legte drei neue Findings 9246–9248 am vorhandenen Besitzer an; die drei ersetzten Findings samt bisherigen aktiven Beziehungen wurden einzeln mit Timeline-Ereignissen obsolete gesetzt. Keine neuen Keys, keine Änderung der vorhandenen dynamischen Übersetzungszeilen oder der ursprünglichen Review-Metadaten. Alle drei aktuellen Findings erhalten im Editor acht Optionen einschließlich deutscher Texte.

Unabhängige Verifikation: 16 korrekte Laufzeitauflösungen, drei passende aktive Findings, acht Editor-Optionen je Finding, unveränderte Werte und Freigaben sowie leerer Folge-Exportplan. Die drei Blade-Views wurden kompiliert und auf PHP-Syntax geprüft. Ein vollständiger Browserdurchlauf erfolgte nicht. Belege und Rücksetzungssicherung: `storage/app/private/translation-category-options-20261013-204713/` mit Plan, ursprünglichen Dateien/Findings, ausgeführtem Skript, `applied.json` und `verified.json`.

Neue Regressionen sichern Veröffentlichung und Laufzeit ohne Datenbankabfragen, Trennung von Punkt-/Prozent-Optionswerten, Originaltext bei fehlender Übersetzung, Schutz skalarer Elternwerte, explizite Key-Auswahl, abgelehnte/ungeprüfte Besitzer und Werte, CLI-Dry-Run sowie Scanner/Rescan und Kommentarerkennung ab. Die zuvor zurückgestellten skalaren Kategorie-Werte wurden durch diesen Schritt nicht veröffentlicht; sie sind weiterhin historische Skalare und keine Optionsübersetzungen.

Abschlussprüfung dieses Schritts: **208 Workbench-Tests / 1092 Assertions erfolgreich**, PHP-Syntaxprüfungen und begrenzter Whitespace-Check ohne Fehler.

## Personen-Stammdaten: Anrede, Geschlecht und Familienstand angeschlossen

Die Bestandsaufnahme aller gespeicherten dynamischen Übersetzungen liegt unter `storage/app/private/translation-option-inventory-20261013-214257/audit.json`. Als nächste eindeutig zugeordnete Gruppe wurden die Optionsanzeigen beim Anlegen und Bearbeiten einer Person umgesetzt.

| Besitzer-Key | Optionen | de/en-Werte | Aktuelle Anzeigen |
| --- | ---: | ---: | --- |
| 3117 – create_person … salutation_options | 3 | 6 | Anlegen und Bearbeiten |
| 3120 – create_person … gender_options | 4 | 8 | Anlegen und Bearbeiten |
| 3137 – create_person … marital_status_options | 7 | 14 | Anlegen |
| 3452 – edit_person … marital_status_options | 7 | 14 | Bearbeiten: Auswahl und Anzeige |

Die vorhandenen geprüften Key-Zuordnungen wurden beibehalten. Die beiden Familienstand-Keys wurden trotz derzeit gleicher Texte nicht nebenbei zusammengeführt. Auch bestehende Formulierungen und Groß-/Kleinschreibung wurden unverändert übernommen.

In `resources/views/components/management/people/create-person/sections/⚡person-core.blade.php` wurden drei Optionsanzeigen an `TranslationWorkbenchOption::label()` angeschlossen, in `resources/views/components/management/people/edit-person/sections/⚡person-core.blade.php` vier. Die Familienstand-Auswahlliste im Bearbeiten-Formular gab bislang `$label` ohne Übersetzungsaufruf aus; auch sie benutzt jetzt den gleichen vorhandenen Besitzer-Key wie die dortige Anzeige. Auswahlwerte, Wire-Bindings, Optionsmengen und Personendaten wurden nicht verändert.

Der bestehende explizite dynamische Export veröffentlichte **42 neue Werte** in `lang/de/management.php` und `lang/en/management.php`. Andere Sprachwerte wurden gegen die Sicherung auf Gleichheit geprüft. Kein neuer Exportmechanismus und kein globaler Export. Der gesamte Abgleich erfolgte gesichert in einer SERIALIZABLE-Transaktion mit Originaldateien, Werte-/Key-/Finding-Snapshot und Rücksetzungsmöglichkeit.

Der fokussierte Scan erstellte sieben aktuelle Findings 9249–9255 an den vier bestehenden geprüften Keys; keine neuen Keys. Die sechs ersetzten aktiven Findings und deren aktive Beziehungen wurden einzeln mit Timeline-Ereignissen obsolete gesetzt. Die zusätzliche siebte Stelle war zuvor der unübersetzte Familienstand-Select. Frühere Reviews und vorhandene dynamische Wertzeilen blieben unverändert.

Unabhängige Prüfung: alle 42 Texte korrekt zur Laufzeit, sieben aktive Findings mit jeweils drei/vier/sieben passenden deutschen Editor-Optionen, unveränderter Übersetzungsbestand und leerer Folge-Exportplan. Beide Blade-Views wurden kompiliert und syntaktisch geprüft. Kein vollständiger Browserdurchlauf. Nachweise: `storage/app/private/translation-person-options-20261013-214418/` mit `plan.json` einschließlich Datei-Sicherung, `apply.php`, `applied.json`, `verify.php` und `verified.json`.

Als nächste Gruppe bieten sich die bereits gepflegten Dokumenttyp-Optionen an. Notfallkontakt-Beziehungen sowie Rollen-Badge-Optionen sind ebenfalls im Inventar enthalten und noch gesondert auf ihre aktuelle Verwendung abzugleichen. Die historischen skalaren Restwerte aus der früheren Fehlstellenliste wurden nicht verändert.

## Dokumenttyp-Optionen: neun Anzeigen angeschlossen

Die aktuelle Prüfung der Dokumenttyp-Aufrufe ist in `storage/app/private/translation-document-options-audit-20261013-221740/audit.json` gesichert. Neun aktive dynamische Aufrufe verwenden dieselbe Dokumenttyp-Optionsliste, waren jedoch teils nur provisorischen Kandidaten zugeordnet. Zwei geprüfte Besitzer enthalten je elf Dokumenttypen in de/en:

- Key 3039: `management.people.create_person.sections.documents.document_type_options` für Auswahllisten.
- Key 3165: `management.people.create_person.form_person.document_type_options` für die Anzeige des gewählten Dokumenttyps bzw. den Titel-Ersatz bei fehlendem Dokumenttitel.

Diese bereits an den bestehenden Aufrufen belegte Unterscheidung bleibt erhalten. Insbesondere unterscheidet sich Englisch für `it_document`: „IT-Document“ an Key 3039 und „IT Document“ an Key 3165. Es erfolgte weder eine redaktionelle Angleichung noch eine Zusammenführung der Besitzer.

Die neun Aufrufe wurden über den vorhandenen `TranslationWorkbenchOption::label()` angeschlossen. Geänderte Views:

- `resources/views/components/management/people/create-person/sections/⚡documents.blade.php` – Auswahl.
- `resources/views/components/management/people/create-person/⚡form-person.blade.php` – Anzeige.
- `resources/views/components/management/people/edit-person/sections/⚡documents.blade.php` – Auswahl und drei Anzeigen/Titel-Ersatzstellen.
- `resources/views/components/management/people/edit-person/sections/documents/⚡add-modal.blade.php` – Auswahl.
- `resources/views/components/management/people/edit-person/sections/documents/⚡archive-modal.blade.php` – Typanzeige und Titel-Ersatz.

Der gezielte dynamische Export veröffentlichte **44 bestehende Werte** in `lang/de/management.php` und `lang/en/management.php`. Die zuvor angeschlossenen Kategorie- und Personenoptionen bleiben erhalten. Optionswerte, Listen, Bindings und Personen-/Dokumentdaten wurden nicht geändert.

Der erste abgesicherte Schreibversuch brach beim strengen PHP-Array-Vergleich ab: Der Exporter sortiert die Arrays, wodurch `===` trotz gleicher Werte fehlschlug. Die Transaktion wurde zurückgerollt und beide Sprachdateien bytegenau auf ihre Sicherungen zurückgesetzt; Views waren noch unverändert. Eine unabhängige Prüfung bestätigte ausschließlich die Reihenfolgeabweichung. Der erneute Abgleich sortiert beide Vergleichsarrays rekursiv und vergleicht weiterhin typstreng sämtliche Schlüssel und Werte. Danach wurde die Umsetzung vollständig abgeschlossen.

Die SERIALIZABLE-Transaktion erstellte neun aktuelle Findings 9256–9264 an den zwei bestehenden geprüften Keys. Die neun ersetzten Findings und ihre aktiven Beziehungen wurden einzeln mit Timeline-Ereignissen obsolete gesetzt. Keine neuen Keys; bestehende Übersetzungszeilen und Freigaben blieben unverändert. Provisorische Alt-Keys wurden nicht pauschal gelöscht oder umbenannt.

Unabhängige Nachprüfung: alle 44 Laufzeittexte korrekt, neun aktive Findings, elf korrekte deutsche Editor-Optionen je Finding, unveränderte gespeicherte Übersetzungen und leerer Folge-Exportplan. Alle fünf Blade-Views wurden kompiliert und syntaktisch geprüft; kein vollständiger Browserdurchlauf. Plan, Datei-/Datensicherung und Nachweise liegen unter `storage/app/private/translation-document-options-20261013-222218/`.

Als nächstes sind die gepflegten Notfallkontakt-Beziehungen zu prüfen; danach die Rollen-Badge-Optionen. Die separate Liste historischer skalarer Fehlstellen bleibt unverändert.

## Notfallkontakt-Beziehungen angeschlossen

Der aktuelle Abgleich liegt unter `storage/app/private/translation-contact-options-audit-20261013-222844/audit.json`. Drei Anzeigestellen verwenden neun Beziehungen: Auswahl beim Anlegen, Auswahl und Anzeige beim Bearbeiten. Zwei vorhandene geprüfte Besitzer enthalten jeweils 18 de/en-Werte:

- Key 3062: `management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options`.
- Key 3315: `management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options`.

Die Bearbeiten-Auswahl (altes Finding 2062) war aktiv mit beiden Keys verbunden. Der neue explizite Aufruf verwendet dort eindeutig Key 3315, ebenso wie die bereits diesem Key zugeordnete Anzeige. Anlegen verwendet weiterhin Key 3062. Die vorhandene unterschiedliche Formulierung für `other` bleibt erhalten: „Sonstige“ beim Anlegen, „Sonstiges“ beim Bearbeiten. Keine redaktionelle Änderung anderer Texte, insbesondere keine stillschweigende Neuübersetzung von `guardian`.

Geändert wurden die Optionsaufrufe in `resources/views/components/management/people/create-person/sections/⚡emergency-contact.blade.php` und `resources/views/components/management/people/edit-person/sections/⚡emergency-contact.blade.php`. Sie nutzen den bestehenden `TranslationWorkbenchOption::label()`-Workflow. Auswahlwerte, Optionsmengen, Wire-Bindings und Personen-/Kontaktdaten bleiben unverändert.

Der gezielte dynamische Export veröffentlichte **36 vorhandene Texte** in `lang/de/management.php` und `lang/en/management.php`. Die Umsetzung wurde durch SERIALIZABLE-Transaktion, unveränderte Daten-/Dateisnapshots, Sicherung und typstrengen Vergleich aller übrigen Sprachwerte abgesichert. Drei neue Findings 9265–9267 wurden an den bestehenden Keys angelegt; die drei ersetzten Findings und ihre vier aktiven Beziehungen wurden einzeln mit Timeline-Ereignissen obsolete gesetzt. Keine neuen Keys, keine Änderung der gespeicherten Übersetzungen oder ursprünglichen Freigaben.

Unabhängige Nachprüfung: alle 36 Laufzeittexte korrekt, drei aktive Findings mit jeweils neun passenden deutschen Editor-Optionen, unveränderter Wertebestand und leerer Folge-Exportplan. Beide Blade-Views wurden kompiliert und syntaktisch geprüft. Kein vollständiger Browserdurchlauf. Plan, Sicherungen und Nachweise: `storage/app/private/translation-contact-options-20261013-223011/`.

Als nächste Gruppe sind die Rollen-Badge-Optionen (Farbe und Variante) zu prüfen. Die historischen skalaren Restwerte bleiben unverändert.

## Rollen-Badges: Farben und Varianten angeschlossen

Vier Anzeigen in `resources/views/components/admin/partials/role-list/⚡modal-create.blade.php` und `resources/views/components/admin/partials/role-list/⚡modal-edit.blade.php` verwenden jetzt den bestehenden Options-Workflow. Als Besitzer dienen die bereits gepflegten, geprüften Keys 2006 (`admin.partials.role_list.modal_edit.role_badge_color_options`) für Farben und 1988 (`admin.partials.role_list.modal_create.role_badge_variant_options`) für Varianten. Es wurden keine zusätzlichen Keys oder Übersetzungstexte erfunden.

Veröffentlicht wurden **44 vorhandene de/en-Werte**: 18 Farben und vier Varianten in je zwei Sprachen, gezielt in `lang/de/admin.php` und `lang/en/admin.php`. Die deutschen Texte sind in dieser Gruppe identisch mit den technischen englischen Bezeichnungen. Sie wurden unverändert übernommen, nicht nachträglich übersetzt.

Beim Abgleich fiel ein konkreter View-Fehler auf: Im Anlegen-Dialog enthielt jede Farboption den sichtbaren Text `label="{{ __('admin.roles.badge.color') }}"`, anstatt den jeweiligen Optionsnamen auszugeben. Diese Stelle rendert jetzt wie die anderen drei Anzeigen das passende Optionslabel. Die Feldüberschrift bleibt separat übersetzt. Farbwerte, Variantenwerte, Optionsmengen, Bindings und tatsächliches Badge-Styling wurden nicht verändert.

Die gesicherte SERIALIZABLE-Umsetzung prüfte Snapshots und Datei-Inhalte, veröffentlichte ausschließlich die geplanten Werte und verglich alle übrigen Sprachwerte typstreng unabhängig von ihrer Sortierung. Vier neue Findings 9268–9271 wurden an den beiden vorhandenen geprüften Keys angelegt. Die vier ersetzten aktiven Findings und deren aktive Beziehungen wurden einzeln mit Timeline-Ereignissen obsolete gesetzt. Der allgemeine Übersetzungs-Key der Farb-Feldüberschrift wurde nicht umbenannt oder stillgelegt; ersetzt wurde nur dessen falsche Verwendung innerhalb der Optionsschleife.

Unabhängige Nachprüfung: alle 44 Laufzeittexte korrekt, vier aktive Findings mit jeweils 18 Farb- oder vier Variantenoptionen im Editor, unveränderte gespeicherte Werte und leerer Folge-Exportplan. Beide Blade-Views wurden kompiliert und syntaktisch geprüft. Kein vollständiger Browserdurchlauf. Plan, ursprüngliche Dateien/Datensätze und Nachweise: `storage/app/private/translation-role-options-20261013-223758/`.

Im Inventar bleibt unter anderem die separat gepflegte dynamische Benutzer-Kategorie (Key 2947) zu prüfen. Eine vollständige Erledigung aller dynamischen Aufrufe ist aus den bisher angeschlossenen Gruppen nicht abzuleiten.

## Rollengruppen-Kategorie im Benutzer-Dialog angeschlossen

Die bislang als „dynamische Benutzer-Kategorie“ bezeichnete Stelle ist konkret die Überschrift einer Rollengruppe in `resources/views/components/admin/partials/user-list/⚡modal.blade.php`. Der vorhandene geprüfte Key 2947 (`admin.partials.user_list.modal.str_headline_category`) enthält eine gepflegte Option: `other`, mit „Other“ in en und „Sonstiges“ in de.

Diese **zwei bestehenden Werte** wurden über den vorhandenen dynamischen Export gezielt in `lang/de/admin.php` und `lang/en/admin.php` veröffentlicht. Der einzelne Aufruf `__(Str::headline($category))` verwendet nun `TranslationWorkbenchOption::label()` mit dem bisherigen Key und dem konkreten Kategorienwert. Für andere, noch unübersetzte Kategorien bleibt die bisherige lesbare Headline als Fallback erhalten. Rollennamen, Gruppenbildung, Radio-Werte, Berechtigungen und Benutzerzuweisungen wurden nicht geändert.

Gesicherte SERIALIZABLE-Umsetzung mit Snapshot- und Datei-Prüfung: ein neues Finding 9272 am vorhandenen Key, ersetztes aktives Finding samt Beziehung einzeln mit Timeline-Ereignissen obsolete gesetzt. Keine neuen Keys, Übersetzungen oder Freigaben. Die gespeicherten Werte blieben unverändert.

Unabhängige Prüfung: beide Texte korrekt zur Laufzeit, aktives Finding mit einer passenden Editor-Option und leerer Folge-Exportplan. Die Blade-View wurde kompiliert und syntaktisch geprüft; kein vollständiger Browserdurchlauf. Sicherung und Nachweise: `storage/app/private/translation-user-category-20261013-224003/`.

Damit ist auch diese konkret gepflegte Gruppe angeschlossen. Als nächstes ist ein abschließender Abgleich des aktuellen dynamischen Bestands sinnvoll: tatsächlich verwendete/veröffentlichte Optionen gegenüber historischen Doppelbeständen und weiterhin ungepflegten Optionen. Aus dem Abschluss der untersuchten Gruppen folgt nicht, dass sämtliche dynamischen Texte der Anwendung bereits übersetzt sind.

## Abschließender aktueller Bestandsabgleich

Der lesende Abgleich bestätigt alle 184 zuletzt angeschlossenen Optionswerte in den Sprachdateien. Weitere 16 gespeicherte Optionswerte gehören zum alten Kategorie-Besitzer 3188 ohne aktuellen Optionshelper-Aufruf. Die zwölf historischen skalaren Exportfehlstellen bestehen weiterhin.

Der aktuelle Quellscan zeigt zugleich erheblichen weiteren Prüf-/Übersetzungsbedarf; daraus lässt sich wegen abweichender Finding-Signaturen noch keine abschließende Zahl fehlender Übersetzungen ableiten. Neun aktuelle Aufrufe mit bereits gespeicherten deutschen Werten sind als nächste Anschluss-Prüfkandidaten dokumentiert. Ausführliche Zahlen, Grenzen und Reihenfolge: [Aktuelle Übersetzungsabdeckung](translation-coverage-status-2026-10-13.md). Es wurden keine Übersetzungen oder Datenbankeinträge verändert.

## Neun verbliebene Anschlussstellen

14 aktive Aufrufe in neun Views (einschließlich fünf Tooltip-Titeln) verwenden nun ihre vorhandenen freigegebenen Keys. Keine Neuübersetzung oder erneute Veröffentlichung. Aktuelle Findings und Timeline wurden abgeglichen; Details und Sicherungsnachweis im [Bestandsbericht](translation-coverage-status-2026-10-13.md#nachtrag-neun-anschlussstellen-umgesetzt).
