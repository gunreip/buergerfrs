# Übersetzungsabdeckung – aktueller Abgleich vom 13.10.2026

> **Abschlussstand 14.10.2026:** Der Finding-/Zuordnungsabgleich ist abgeschlossen. Alle 18.824 Aufrufe im bearbeiteten Scan-Bereich besitzen aktive Findings und Beziehungen; die Gesamtprüfung meldet keine Key-Zuordnungsabweichungen. 155 Aufrufe aus ausdrücklich ausgesparten Altansichten und ein leerer Aufruf sind separat erklärt. Noch fehlende Übersetzungen bleiben reguläre Workbench-Aufgaben, keine unerfassten Fundstellen. Die folgenden Anfangszahlen dokumentieren den historischen Ausgangsstand; die vollständigen Abschlusszahlen und Nachweise stehen im Abschnitt „Abschluss des zusammenhängenden Bestandsabgleichs vom 14.10.2026“ am Ende.

## Umfang und Grenzen

Lesender Scan der konfigurierten Projekt- und Package-Pfade; auskommentierte Aufrufe ausgeschlossen. Ziel dieses Abgleichs ist Deutsch (`de`), ohne englischen Sprach-Fallback. Erfasst werden vom bestehenden Scanner erkannte Übersetzungsaufrufe, nicht beliebige unmarkierte Texte. Eine vorhandene Übersetzung wird nicht sprachlich bewertet. Technische Namen dürfen in beiden Sprachen gleich lauten.

13.843 der 18.980 aktuellen Quellstellen haben keinen exakt passenden aktiven Finding-Eintrag über die `source_signature`. Das kann auch durch verschobene Zeilen oder geänderte Aufrufe entstehen. Diese Zahl bedeutet weder ebenso viele neue Texte noch ebenso viele verlorene Übersetzungen. Bei diesen Stellen fehlen im Audit unter Umständen historische Besitzer-Zuordnungen. Daher sind die Fehlstellen **Prüfkandidaten, keine endgültige Übersetzungs- oder Löschliste**.

## Ergebnis

| Bereich | Ergebnis |
| --- | ---: |
| Aktuelle erkannte Aufrufe | 18.980 |
| Skalare Aufrufe mit direkt auflösbarem deutschem Text | 1.783 |
| Skalare Aufrufe ohne nachgewiesenen deutschen Wert | 17.134 |
| Unterschiedliche Eingabetexte/Keys dieser Prüfkandidaten | 6.726 |
| Aufrufe mit gespeichertem deutschem Besitzer-/Sprachwert, aber ohne direkte Auflösung | 9 |
| Dynamische Aufrufe insgesamt | 54 |
| Davon ohne ermittelte Optionsliste | 26 |
| Veröffentlichte und aktuell über Optionshelper verwendete Werte | 184 |
| Gespeicherte Optionswerte ohne aktuellen Optionshelper-Aufruf | 16 |

Die 26 dynamischen Aufrufe ohne Optionsliste sind nicht automatisch unübersetzt: Dazu gehören auch durchgereichte Component-Props und statisch aufgelöste dynamische Aufrufe. Bei den ermittelten Optionslisten fehlen keine deutschen Zielwerte. Unbekannte bzw. künftig hinzukommende Optionen sind dadurch nicht abgedeckt. Die 16 weiteren gespeicherten Werte gehören zum bisherigen Kategorie-Besitzer Key 3188; sie wurden nicht gelöscht.

## Bereits gespeicherte Werte: Anschluss prüfen

Die folgenden Stellen besitzen deutsche Werte über ihre gespeicherten Beziehungen, lösen aber unter dem aktuellen Aufruf keinen deutschen Text auf. Mehrfachzuordnungen und die tatsächliche Verwendung der View müssen vor einer Änderung geprüft werden.

| Datei | Zeile | Aktueller Text/Key | Besitzer-IDs |
| --- | ---: | --- | --- |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit.blade.php` | 17 | `Edit translation values` | 330, 412, 4166, 4217 |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-review.blade.php` | 13 | `Review finding` | 329, 441, 3867, 4216 |
| `resources/views/components/admin/partials/activity-log/⚡filter.blade.php` | 39 | `Timespan` | 1136, 1214, 1288 |
| `resources/views/components/admin/partials/role-list/⚡modal-create.blade.php` | 55 | `ui.sort-order` | 1843, 5514 |
| `resources/views/components/admin/partials/role-list/⚡modal-edit.blade.php` | 77 | `ui.sort-order` | 1843, 5515 |
| `resources/views/components/admin/partials/translation-sub-languages/⚡table.blade.php` | 73 | `Sub-languages` | 437, 768, 2672, 2709 |
| `resources/views/components/admin/partials/translation-usage-audit/⚡table.blade.php` | 177 | `UI candidate` | 273, 487, 2739, 2889, 4227 |
| `resources/views/components/management/people/create-person/sections/⚡person-core.blade.php` | 48 | `Test data` | 134, 3113, 3490, 3508, 3556 |
| `resources/views/components/management/people/edit-person/sections/⚡contact.blade.php` | 30 | `Phone` | 147, 1522, 3027, 3063, 3249, 3339 |

## Gespeichert, aber nicht exportiert

Der separate aktuelle Sprachdatei-Abgleich bestätigt zwölf fehlende skalare Werte (sechs Keys in de/en). Es handelt sich weiterhin um DEV-/auskommentierte Testtexte, den alten `ui.assignable-through-ui` und einen skalaren Kategorie-Text. Sie sind keine Liste der insgesamt fehlenden Übersetzungen. Der globale trockene Export würde zusätzlich zehn vorhandene Werte ändern und 215 Werte entfernen. Diese Vorschläge wurden **nicht ausgeführt** und sind keine Freigabe zur Bereinigung.

## Sinnvolle nächste Reihenfolge

1. Die neun Anschluss-Prüfkandidaten anhand ihrer Besitzer, Freigaben und aktuellen Views klären; vorhandene Werte nicht neu übersetzen.
2. Den Unterschied zwischen aktuellem Quellscan und gespeicherten Findings über den regulären Scan-/Review-Workflow abgleichen. Keine pauschale Stilllegung oder neue Besitzer-Zuordnung aufgrund dieses Audits.
3. Danach fehlende Übersetzungen nach Bereichen bearbeiten: aktive Workbench-Oberfläche, übrige Anwendung, TW-Graph/Dokumentation. Technische Beispiele und reine Bezeichner gesondert prüfen.

## Nachweise

- Quellstellen-/Optionsabgleich: `storage/app/private/translation-current-coverage-20261013-224836/audit.json` mit ausgeführtem `audit.php`.
- Sprachdatei-Abgleich: `storage/app/private/translation-unpublished-audit-20261013-224703/audit.json`.
- Beide Datenbankabgleiche liefen in schreibgeschützten Transaktionen. Timeline-Anzahl und SHA-256-Hashes der Sprachdateien blieben unverändert.
- Keine Übersetzungen, Quellaufrufe, Freigaben oder Datenbankeinträge geändert. Kein Browsercheck erforderlich; keine Änderung am Anwendungscode.

## Nachtrag: neun Anschlussstellen umgesetzt

Die neun Prüfkandidaten sind angeschlossen, einschließlich fünf identischer Tooltip-Titel: **14 aktive Aufrufe in neun Views**. Verwendet werden ausschließlich die bereits freigegebenen und veröffentlichten acht unterschiedlichen Keys aus der obigen Tabelle. Es wurden keine Texte neu übersetzt, keine Sprachdateien geändert und keine Keys oder Übersetzungsfreigaben angelegt. Vorhandene Formulierungen, auch „Sprachvarainten“ und „Ergebnis der Überprüfung“, bleiben unverändert und sind gegebenenfalls separat redaktionell zu prüfen.

Der reguläre Code-Update-Planner bestätigte zunächst sieben sichere Änderungen. Zwei Dialogtitel wurden wegen älterer namenloser Kandidaten mit kleineren IDs nicht eingeplant (Auswahl über MIN(key_id)). Die sechs betreffenden Beziehungen wurden nachvollziehbar obsolete gesetzt; die Kandidaten selbst bleiben erhalten. Danach bestätigte der Planner alle neun Stellen.

Fünf Views enthalten zusätzlich denselben Text im Tooltip-Titel. Diese aktuell nicht zugeordneten Aufrufe verhindern die direkte automatische Anwendung eines Plans, der jeweils nur eine Fundstelle kennt. Daher erfolgte die begrenzte Wiederherstellung anhand der exakten aktiven Scanner-Aufrufe mit vollständigem Vorher-/Nachher-Vergleich; auskommentierte i18n-native-Aufrufe blieben unangetastet. Der allgemeine Planner/Applier wurde nicht geändert. Anschließend wurden alle 14 Aufrufe über den Foundation-Syncer erfasst; neun ersetzte Findings samt noch aktiven Beziehungen wurden mit Timeline-Nachweisen stillgelegt. Keine pauschale Obsoletion anderer Findings.

Sicherung, Snapshots, ausgeführtes Skript und Ergebnisse: `storage/app/private/translation-nine-apply-20261013-225518/`. Die Datenbankumsetzung lief in einer SERIALIZABLE-Transaktion mit Datei-Rücksetzung im Fehlerfall. Alle gespeicherten Besitzer-, Freigabe- und Übersetzungsdatensätze wurden unverändert nachgewiesen. Sämtliche Sprachdatei-Hashes blieben gleich; alle neun Blade-Views wurden kompiliert und mit PHP geprüft. Der Folgeplan enthält 15 bereits aktuelle Aufrufe (14 neue plus einen bestehenden) und keine offene sichere Änderung. Kein Browserdurchlauf.

Die ursprünglichen Inventarzahlen oben bleiben als Ausgangsmessung erhalten. Nächster offener Workflow-Punkt: aktuelle Quellstellen und gespeicherte Findings abgleichen, insbesondere noch nicht erfasste Tooltip-Titel und die Besitzer-Auswahl bei mehreren aktiven Beziehungen. Erst danach ergibt sich eine belastbarere Liste tatsächlich fehlender Übersetzungen.

## Nachtrag: Quellstellen-Abweichung und Planner-Korrektur

Der erneute lesende Scan nach den 14 Anschlusskorrekturen ergibt 18.980 aktuelle, nicht auskommentierte Aufrufe:

| Zuordnung | Anzahl |
| --- | ---: |
| Exakte Signatur, aktives Finding | 5.137 |
| Exakte Signatur, inaktives Finding | 19 |
| Gleiche Datei und gleicher Ausdruck-Fingerprint | 164 |
| Gleiche Datei und gleicher semantischer Fingerprint | 2 |
| Kein entsprechender Treffer in derselben Datei | 13.658 |

Die letzte Gruppe umfasst 11.802 TW-Graph-Aufrufe, 1.835 weitere Package-Aufrufe und 21 Projekt-Aufrufe. Das ist kein Nachweis, dass alle diese Texte neu oder unübersetzt sind: Datei-Verschiebungen, bestehende Übersetzungen unter anderen Besitzern und technische Dokumentationsbeispiele sind damit nicht abschließend geklärt. Die 166 Ausdruck-/Semantik-Treffer sind Zuordnungskandidaten, keine automatisch freigegebenen Beziehungen. Unter den 19 inaktiven exakten Treffern ist auch ein leerer `__('')`-Aufruf; pauschale Reaktivierung wäre deshalb falsch.

Nachweis mit allen Quellstellen und Kandidaten-IDs: `storage/app/private/translation-finding-drift-20261013-225837/audit.json`. Keine Produktionsdaten geändert.

Ein reproduzierbarer Workflow-Fehler wurde im `TranslationWorkbenchCodeUpdatePlanner` korrigiert: Vorher wurde zuerst die kleinste aktive Besitzer-ID ausgewählt und erst danach geprüft, ob sie einen freigegebenen Key besitzt. Damit konnten alte namenlose/pending Kandidaten die tatsächlich vorhandene Freigabe verdecken. Jetzt berücksichtigt die Auswahl nur offene, freigegebene, benannte Besitzer. Mehrere Besitzer mit identischem Key gelten als dieselbe Zielbenennung; verschiedene freigegebene Keys erzeugen ausdrücklich `manual_review` / `multiple_reviewed_translation_keys`. Ein exakter Key-Filter darf diesen Konflikt nicht ausblenden. Der Applier ändert bei einem solchen Konflikt keine Quelldatei.

Regressionstest ergänzt: älterer namenloser Kandidat, späterer freigegebener Besitzer, doppelte Besitzer desselben Namens, konkurrierende Namen mit/ohne Filter, Schreibschutz bei Konflikt und obsolete Besitzer. Die allgemeine Scanner-Signatur und die automatische Stilllegung wurden nicht geändert.

## Nachtrag: 166 Zuordnungskandidaten geprüft, 115 Verschiebungen abgeglichen

Die Detailprüfung unterscheidet:

| Ergebnis | Stellen | Behandlung |
| --- | ---: | --- |
| Eindeutige Verschiebung bei gleichem Ausdruck und gleicher Klassifizierung | 115 | Aktualisiert |
| Mehrere aktuelle Vorkommen desselben Ausdrucks | 45 | Keine automatische Zuordnung |
| Nur semantische Übereinstimmung | 2 | Offen zur Einzelprüfung |
| Kein eindeutiger aktiver Vorgänger | 4 | Offen zur Einzelprüfung |

Audit: `storage/app/private/translation-drift-candidates-20261013-230158/audit.json` mit Kandidaten-IDs und aktuellen Quellstellen.

Der bestehende `TranslationWorkbenchFoundationSyncer` erkennt nun eindeutige Verschiebungen, wenn keine exakte Signatur vorhanden ist. Voraussetzung sind dieselbe Datei, derselbe Ausdruck-Fingerprint, dieselbe Art und derselbe vorgeschlagene Key, genau ein aktiver Vorgänger und genau ein aktuelles Vorkommen im vollständigen File. Auch bei einem Teilscan wird das vollständige File auf Mehrfachvorkommen geprüft. Historische/inaktive Vorgänger oder reine semantische Ähnlichkeit werden nicht automatisch übernommen. Die globale Signaturdefinition bleibt unverändert.

Die 115 eindeutigen Stellen in sieben Dateien wurden über diesen Syncer aktualisiert. Alle Finding-IDs und bestehenden Beziehungen blieben erhalten; 115 reguläre `finding_changed`-Timeline-Ereignisse dokumentieren die Änderungen. Keine neuen Findings oder Keys, keine Stilllegungen, keine Änderungen an Quelldateien, Übersetzungswerten oder Freigaben. Die im Sync-Bericht genannten 115 `relations_updated` sind aufgerufene Aktualisierungen; der gespeicherte Beziehungsbestand wurde danach vollständig unverändert verglichen.

Gesicherte SERIALIZABLE-Umsetzung: `storage/app/private/translation-drift-apply-20261013-230328/` mit Vorher-Snapshot, geschützten Daten-Hashes und ausgeführtem Skript. Die vier Werte-/Review-Tabellen wurden vollständig auf unveränderten Inhalt geprüft. Regressionstests decken den Erhalt von ID/Freigabe/Beziehung/Timeline sowie den Ausschluss mehrfacher Aufrufe bei einem Teilscan ab. Gesamter Translation-Workbench-Testlauf: **211 Tests, 1.119 Assertions erfolgreich**. PHP-Syntax und Diff-Whitespace sauber; kein Browserdurchlauf.

Die übrigen 51 Kandidaten sind weiterhin offen und wurden nicht pauschal synchronisiert oder umgehängt. Sie müssen vor der anschließenden Bestandsaufnahme fehlender Übersetzungen gesondert zugeordnet werden.

## Nachtrag vom 14.10.: weitere 24 Zuordnungen nachgeführt

Von den 51 offenen Stellen konnten 22 mehrfach vorkommende Aufrufe anhand unveränderter, eindeutig lokalisierbarer Git-Codeblöcke ihrer bisherigen Finding-ID zugeordnet werden. Der geprüfte Git-Commit, Block-Hashes, alte/neue Zeilen und Datei-Hashes sind im Plan dokumentiert. Die Zuordnung beruht nicht allein auf Reihenfolge oder dem geringsten Zeilenabstand.

Zwei weitere Aufrufe unterscheiden sich durch Formatierung und ein optionales abschließendes Komma (`__('key')` → `__( 'key', )`). Ein strenger Tokenvergleich stoppte deshalb zunächst vor jeder Datenänderung. Nach gesonderter Prüfung ausschließlich dieser einparametrigen String-Aufrufe wurden auch ihre bestehenden IDs erhalten. Die allgemeine Scanner-/Syncer-Logik wurde dafür nicht gelockert.

Die beiden gesicherten SERIALIZABLE-Umsetzungen aktualisierten 24 bestehende Findings über den Foundation-Syncer. Die explizite Identitätszuordnung und die anschließenden Metadatenänderungen sind getrennt in der Timeline vermerkt. Keine neuen Findings oder Keys, keine Stilllegungen; sämtliche Beziehungen, Übersetzungswerte und Freigaben blieben nachweislich unverändert. Quelldateien wurden nicht bearbeitet. Alle vier Werte-/Review-Tabellen wurden vor/nach der Umsetzung über vollständige Inhalts-Hashes verglichen.

Nachweise:

- `storage/app/private/translation-history-mapping-20261014-031059/` – 22 Git-belegte Verschiebungen.
- `storage/app/private/translation-history-mapping-20261014-031127/` – zwei formatierte Aufrufe mit optionalem Komma.
- `storage/app/private/translation-finding-drift-20261014-031148/audit.json` – unabhängiger erneuter Gesamtabgleich: 5.276 exakte aktive Treffer, 27 Ausdruckskandidaten, keine verbleibenden rein semantischen Kandidaten aus dieser Gruppe. Die 19 exakten inaktiven und 13.658 sonstigen Stellen sind unverändert.

In diesem Schritt wurde kein Anwendungscode geändert; die Verifikation erfolgte über Snapshot-, Daten-, Quellhash- und frischen Scanner-Abgleich. Kein Browserdurchlauf und kein erneuter Pest-Lauf; letzter Anwendungsteststand bleibt 211 Tests / 1.119 Assertions.

### Verbleibende 27 Stellen

Diese Stellen wurden geprüft, aber ohne ausreichend eindeutigen Identitätsnachweis nicht umgehängt. Bei Mehrfachaufrufen ist ein weiterer historischer Quellstand bzw. der konkrete Aufrufkontext zu prüfen. Drei Sidebar-Aufrufe besitzen nur obsolete Vorgänger; der Titel „Translation Workbench“ zwei aktive Vorgänger. Sie werden nicht pauschal reaktiviert oder zusammengelegt.

| Datei | Zeile | Aufruf | Einordnung |
| --- | ---: | --- | --- |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/export-report.blade.php` | 618 | `__('Actions')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/export-report.blade.php` | 653 | `__('None')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/export-report.blade.php` | 661 | `__('None')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/export-report.blade.php` | 697 | `__('Show in Work findings')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/export-report.blade.php` | 708 | `__('Show in Work findings')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/export-report.blade.php` | 714 | `__('No finding context available')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/export-report.blade.php` | 726 | `__('No finding context available')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 158 | `__('ui.filters.yes')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 158 | `__('no')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 159 | `__('ui.filters.yes')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 159 | `__('no')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 168 | `__('ui.filters.yes')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 168 | `__('no')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 374 | `__('Cell value')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 405 | `__('Open in VSC')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 414 | `__('Open source in VS Code')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 432 | `__('Open in VSC')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 441 | `__('Open source in VS Code')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 511 | `__('ui.key.suggested-key')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 577 | `__('ui.key.suggested-key')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-card.blade.php` | 621 | `__('Cell value')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/settings.blade.php` | 112 | `__('Defaults')` | Mehrfachaufruf; Identität noch offen |
| `packages/gunreip/laravel-translation-workbench/resources/views/livewire/settings.blade.php` | 138 | `__('Defaults')` | Mehrfachaufruf; Identität noch offen |
| `resources/views/layouts/app/sidebar/⚡administration.blade.php` | 75 | `__('Translation Workbench')` | Aktiver Vorgänger nicht eindeutig bzw. nur historische Vorgänger |
| `resources/views/layouts/app/sidebar/⚡administration.blade.php` | 356 | `__('layouts.sidebar.administration.countries')` | Aktiver Vorgänger nicht eindeutig bzw. nur historische Vorgänger |
| `resources/views/layouts/app/sidebar/⚡administration.blade.php` | 366 | `__('admin.permissions.table.columns.flags')` | Aktiver Vorgänger nicht eindeutig bzw. nur historische Vorgänger |
| `resources/views/layouts/app/sidebar/⚡administration.blade.php` | 394 | `__('layouts.sidebar.administration.fallback_reports')` | Aktiver Vorgänger nicht eindeutig bzw. nur historische Vorgänger |

## Nachtrag vom 14.10.: ältere Quellstände und Sidebar-Historie

Vier weitere Zuordnungen konnten durch ältere Git-Stände mit unveränderten, eindeutig vorkommenden Codeblöcken belegt werden:

- `settings.blade.php`: die beiden `Defaults`-Aufrufe, Findings 9084 und 9087, jetzt Zeilen 112 und 138.
- `raw-data/table-card.blade.php`: Ja/Nein für `Sortable`, Findings 6669 und 6671, jetzt Zeile 168.

Die drei anderen Ja/Nein-Paare wurden dadurch nicht automatisch zugeordnet: Sie gehören zu anderen Metadaten-Feldern. Exakte Zuordnungsnachweise, Quellhashes, Vorher-Bestand und ausgeführtes Skript: `storage/app/private/translation-history-mapping-20261014-031500/`.

Die Umsetzung verwendete dieselben abgesicherten Schritte wie zuvor: SERIALIZABLE-Transaktion, explizite Identitätszuordnung mit Timeline und Foundation-Sync unter denselben IDs. Keine neuen Findings, Keys oder Beziehungen, keine Stilllegungen, keine Quelländerungen. Beziehungen, Schlüssel/Freigaben und die vollständigen Werte-/Review-Tabellen blieben nachweislich unverändert.

Zusätzlich wurde die Sidebar-Timeline gezielt gelesen (`storage/app/private/translation-remaining-history-20261014-031507/audit.json`). Die jüngeren Vorgänger für Countries, Flags und Fallback Reports (9096, 9097, 9099) wurden bereits am 07.08.2026 wegen `not_seen_in_latest_foundation_sync` stillgelegt. Ihre damaligen Besitzer sind pending, nicht freigegeben. Daraus lässt sich keine heutige Übersetzungsfreigabe und keine eindeutige automatische Reaktivierung ableiten. Der aktuelle Titel „Translation Workbench“ hat zwei aktive historische Vorgänger (214, 4798); ein freigegebener Key `ui.translation.translation-workbench` existiert, die Identität des konkreten Aufrufs bleibt aber gesondert zu klären.

Damit sind von den ursprünglichen 166 Kandidaten 143 unter Beibehaltung ihrer IDs nachgeführt; 23 bleiben offen (19 Mehrfachaufrufe und vier Sidebar-Stellen). Für diese wurden keine Zuordnungen nach bloßer Reihenfolge oder Zeilenabstand erzwungen. Anwendungscode und Übersetzungen wurden in diesem Schritt nicht geändert; Validierung über Bestandsvergleiche und erneuten vollständigen Scanner-Abgleich, kein neuer Pest-/Browserlauf.

## Abschluss der 166 Kandidaten: heutiger Bestand und historische Identität getrennt

Die verbleibenden 23 aktuellen Aufrufe wurden am 14.10.2026 über einen gezielten Foundation-Sync neu erfasst. Eine sichere Zuordnung zu einzelnen alten IDs war nicht vollständig belegbar; deshalb wurde keine solche Identität behauptet. Die 143 bereits belegten Zuordnungen behalten ihre alten IDs, die letzten 23 besitzen neue Finding-IDs.

Anschließend wurden ausschließlich aktive historische Kandidaten aus dieser begrenzten Gruppe stillgelegt, deren Signatur im vollständigen aktuellen Scan der betreffenden Dateien nicht mehr vorkommt. Das betraf ebenfalls 23 Findings; die gleiche Anzahl bedeutet keine behauptete Eins-zu-eins-Nachfolge. Deren aktive Beziehungen wurden mit Timeline-Ereignissen obsolete gesetzt. Aktuelle, bereits exakt passende Findings blieben unverändert. Alle historischen Zeilen und Ereignisse bleiben erhalten; keine Löschung und keine allgemeine `markObsolete`-Operation.

Sicherung und Ergebnisse: `storage/app/private/translation-occurrences-20261014-031723/`. Die SERIALIZABLE-Transaktion prüfte aktuelle Quellhashes, bestehende Schlüsselbezeichnungen/Freigaben sowie vollständige Inhalts-Hashes der vier Werte-/Review-Tabellen. Es wurden keine neuen Keys oder Übersetzungsfreigaben erstellt, keine Übersetzungen oder Views geändert. Die neuen Fundstellen sind mit vorhandenen Key-Kandidaten verbunden; ihre Erfassung ist keine Freigabe zur automatischen Übersetzung oder Code-Ersetzung.

Der unabhängige erneute Gesamtscan (`storage/app/private/translation-finding-drift-20261014-031736/audit.json`) bestätigt:

| Kategorie | Stellen |
| --- | ---: |
| Exakte aktive Findings | 5.303 |
| Verbleibende Ausdrucks-/Semantik-Kandidaten | 0 |
| Exakte, aber inaktive Findings | 19 |
| Ohne entsprechenden bisherigen Treffer in derselben Datei | 13.658 |

Damit ist diese Gruppe von 166 Kandidaten abgearbeitet. Die Gesamtinventur ist weiterhin offen: Als nächstes sind die 19 inaktiven exakten Treffer anhand ihres Stilllegungsgrunds zu prüfen; danach können die bislang nicht erfassten Aufrufe getrennt nach Anwendung, Workbench und TW-Graph eingeordnet werden. Eine massenhafte Reaktivierung oder Veröffentlichung ist daraus nicht abzuleiten.

## Nachtrag vom 14.10.: 19 exakte inaktive Treffer geprüft

18 der 19 Aufrufe werden aktuell außerhalb von Kommentaren verwendet und waren zuvor mit `not_seen_in_latest_foundation_sync` stillgelegt worden. Sie wurden gezielt über den Foundation-Syncer unter ihren vorhandenen IDs reaktiviert. Der leere Aufruf `__('')` (Finding 2612) bleibt mit `empty_literal_translation_call` unverändert obsolete und wird nicht als fehlende Übersetzung behandelt.

Bei zwölf `ui.title.overview`-Aufrufen existieren historische Beziehungen zu `ui.title.filter`. Diese wurden nicht reaktiviert. Nach der Synchronisierung wurde für jeden expliziten Key geprüft, dass sämtliche aktiven Beziehungen den tatsächlich aufgerufenen Key verwenden. Die übrigen Stellen betreffen Ja/Nein in den Tabellenmetadaten, `ui.states.all` im Personenfilter und drei Reset-Aufrufe. Die Erfassung eines aktuellen Aufrufs vergibt keine Übersetzungsfreigabe; vorhandene pending-/reviewed-Werte bleiben erhalten.

Die Umsetzung aktualisierte 18 bestehende Findings in 15 Dateien, ohne neue Findings oder Keys und ohne Quelldateiänderungen. Für die aktuellen Besitzer entstanden 15 Beziehungen; drei bestehende Beziehungen wurden aktualisiert. Alle Schlüsselbezeichnungen und Freigabefelder sowie sämtliche Einträge der vier Werte-/Review-Tabellen wurden vor/nach der SERIALIZABLE-Transaktion identisch nachgewiesen. Der leere Aufruf blieb vollständig unverändert. Der Syncer dokumentierte 36 Timeline-Ereignisse.

Nachweise:

- `storage/app/private/translation-inactive-audit-20261014-031851/audit.json` – Stilllegungsgründe und Historie.
- `storage/app/private/translation-inactive-apply-20261014-031951/` – Sicherung, ausgeführtes Skript und Ergebnis.

Separater Quellhinweis: In `resources/views/components/management/people/person-overview/⚡filter.blade.php` stehen an Zeilen 158/159 zwei `__('ui.states.all')`-Aufrufe direkt nacheinander, gefolgt von einem Test-/TODO-Kommentar. Beide existieren tatsächlich im Quellcode. Die sichtbare Verdoppelung wurde in diesem Bestandsabgleich nicht entfernt; sie ist unabhängig von der Finding-Reaktivierung zu korrigieren.

In diesem Schritt änderte sich kein Anwendungscode. Prüfung durch Daten-/Review-Snapshots, Quellhashes und erneuten vollständigen Scan; kein zusätzlicher Pest-/Browserlauf.

## Nachtrag vom 14.10.: 21 bisher nicht zugeordnete Projektaufrufe

Die 21 Stellen außerhalb des Packages wurden geprüft und gezielt über den Foundation-Syncer erfasst:

- Drei Aufrufe liefern bereits deutsche Texte: `ui.states.all` in der älteren TW-Graph-v2-Trunk-Komponente, die Beschreibung im Zwei-Faktor-Setup und `ui.welcome.welcome` auf der Startseite. Ihnen fehlte das aktuelle Finding, nicht die Übersetzung.
- Der Sidebar-Aufruf `__('Workflow')` verwendet nun den vorhandenen freigegebenen Key `ui.nouns.workflow` (Key 2063), mit „Arbeitsablauf“ in de und „Workflow“ in en. Das ist die einzige Quelländerung dieses Schritts: `resources/views/layouts/app/sidebar/⚡administration.blade.php`, Zeile 104.
- 17 Texte haben im geprüften Bestand keine passende deutsche Übersetzung: zwei Default-Labels der älteren TW-Graph-v2-Komponente und 15 Sidebar-Menü-/Beispieltitel. Dazu zählen auch technische Bezeichnungen wie „TW-Graph“; die Erfassung als Kandidat bedeutet nicht, dass diese zwingend anders formuliert werden müssen.

Der Sync erstellte 21 Findings und 21 Beziehungen sowie 18 noch ungeprüfte Keys. Die Key-Anzahl ist nicht die Anzahl fehlender Übersetzungen: Auch bereits veröffentlichte Keys können noch keinen passenden Foundation-Kandidaten besitzen; andererseits können vorhandene Kandidaten wiederverwendet werden. Alle neu angelegten Keys wurden ausdrücklich auf `pending` und fehlendes `reviewed_at` geprüft. Bestehende Freigaben und sämtliche Einträge der vier Werte-/Review-Tabellen blieben unverändert. Keine neue Übersetzung, kein Export und keine pauschale Stilllegung.

Audit: `storage/app/private/translation-project-new-20261014-032125/audit.json`. Gesicherte SERIALIZABLE-Umsetzung einschließlich ursprünglicher Dateien: `storage/app/private/translation-project-new-apply-20261014-032213/`. Der neue Workflow-Aufruf ist nachweislich aktiv mit dem bisherigen freigegebenen Besitzer 2063 verknüpft. Der vollständige Vorher-/Nachher-Vergleich bestätigt ausschließlich diese eine Quelländerung. Die Sidebar wurde als Blade kompiliert und mit PHP geprüft. Kein Browserdurchlauf.

## Nachtrag vom 14.10.: aktive Workbench-Klasse erfasst

Die bisherige Kategorie `package_other` ist lediglich eine Pfadklassifikation, keine Aussage über aktive Workbench-Funktionen: Sie enthält auch `Support/TwGraph/Documentation/OverviewStructure.php`, eingebettete Graph-Previews und stillgelegte Klassen. Im ServiceProvider sind `TranslationWorkbenchOldEntries` und `TranslationWorkbenchRawData` ausdrücklich auskommentiert. Diese wurden in diesem Schritt nicht reaktiviert oder synchronisiert.

Bearbeitet wurde ausschließlich die aktiv registrierte Klasse `src/Livewire/TranslationWorkbenchEntries.php` im Package:

- 389 bisher nicht zugeordnete aktuelle Literal-Aufrufe, 336 unterschiedliche Ausdrücke.
- Kein Aufruf liefert unter seinem aktuellen Literal direkt eine deutsche Übersetzung.
- Für 17 Aufrufe existieren passende englische Texte unter anderen Keys mit deutschen Werten. Das sind Zuordnungskandidaten, keine automatisch freigegebenen Ersetzungen. Beispielsweise bestehen für „Suggested key“ unterschiedliche deutsche Formulierungen; andere Kandidaten besitzen mehrere mögliche Keys oder einen pending-Status.
- Die restlichen 372 Aufrufe besitzen im geprüften Abgleich keine solchen deutschen Lang-Value-Kandidaten. Dies ist keine Behauptung, dass semantisch ähnliche Übersetzungen grundsätzlich nicht existieren.

Über den bestehenden Foundation-Sync wurden 389 aktive Findings, 389 Beziehungen und 336 neue pending-Key-Kandidaten angelegt. 53 wiederholte Ausdrücke verwendeten die im selben Lauf neu erfassten Kandidaten erneut. Der Sync dokumentierte 1.115 Timeline-Ereignisse. Keine Quelländerung, keine Übersetzung, keine Freigabe, kein Export und keine Stilllegung.

Die SERIALIZABLE-Transaktion prüfte den Quellhash vor/nach dem Sync, sämtliche vorhandenen Finding-/Beziehungszeilen sowie bestehende Key-Namen/Freigabefelder und alle vier Werte-/Review-Tabellen auf unveränderten Inhalt. Neue Keys wurden auf pending und fehlendes Freigabedatum geprüft. Audit und Sicherung:

- `storage/app/private/translation-active-package-20261014-032645/`
- `storage/app/private/translation-active-package-apply-20261014-032728/`

Der unabhängige vollständige Nachscan (`translation-finding-drift-20261014-032739`) bestätigt 5.731 exakt aktive Aufrufe und 13.248 weiterhin nicht zugeordnete Aufrufe. Davon liegen 1.446 in der groben Kategorie `package_other`, 11.802 im Pfadbereich `/tw-graph/`. Der einzelne inaktive leere Aufruf bleibt absichtlich unverändert. Kein neuer Pest-/Browserlauf, da ausschließlich der Datenbestand über den bestehenden Sync abgeglichen wurde; Validierung durch Transaktionsprüfungen und unabhängigen Gesamtscan.

Als Nächstes: die 17 vorhandenen Übersetzungskandidaten im konkreten Verwendungskontext prüfen und anschließend die übrigen aktiven Package-Views getrennt von Dokumentation und stillgelegten Klassen abgleichen.

## Nachtrag vom 14.10.: 17 Übersetzungskandidaten der aktiven Workbench kontextuell geprüft

Acht Aufrufe in `src/Livewire/TranslationWorkbenchEntries.php` verwenden jetzt vorhandene, freigegebene Keys, die bereits in den zugehörigen Workbench-Views eingesetzt werden:

| Aktueller Text | Stellen | Übernommener Key |
| --- | ---: | --- |
| Translation key missing | 6 | `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.translation_key_missing` |
| UI candidate | 1 | `ui.ui.ui-candidate` |
| UI translation | 1 | `ui.ui.ui-translation` |

Die fehlende-Key-Meldung betrifft Edit-/Review-Aktionen derselben Workbench; die beiden UI-Bezeichnungen betreffen die Kandidatenfilter, deren Tabellenzellen bereits dieselben Keys verwenden. Die bestehenden Laufzeitwerte wurden in de/en vor der Änderung exakt geprüft. Keine neue Übersetzung oder Freigabe.

Neun Stellen bleiben bewusst unverändert:

- Viermal „No changes“ und einmal „System“: passende gespeicherte Werte, aber die geprüften Besitzer sind pending.
- „Review“: hinterlegtes „Rezension“ passt nicht zum Review-/Prüfereignis der Timeline.
- „Cleared“: meint hier einen entfernten Wert; „Freigegeben“ wäre in diesem Kontext falsch, der Kandidat ist zudem pending.
- „Suggested key“: der freigegebene Kandidat enthält „Vorgeschlagene Schlüssel“, während der andere Kandidat „Vorgeschlagener Schlüssel“ pending ist. Keine stille Änderung des vorhandenen Übersetzungswerts.
- „Locales“: „Lokale“ ist für die technischen Sprach-/Locale-Angaben nicht eindeutig passend; keine Übernahme nur aufgrund desselben englischen Worts.

Sicherung und Ausführung: `storage/app/private/translation-context-eight-20261014-043328/`. Der gezielte Sync erfasste acht neue aktuelle Key-Aufrufe mit acht Beziehungen zu bestehenden freigegebenen Besitzern. Die acht ersetzten Literal-Findings und ihre aktiven Beziehungen wurden mit Timeline-Nachweis stillgelegt; historische Daten bleiben erhalten. Keine allgemeine Stilllegung, keine neuen Keys. Die SERIALIZABLE-Transaktion bestätigte unveränderte Key-Namen/Freigaben, Werte-/Review-Tabellen und alle nicht betroffenen Findings. Syntaxprüfung der geänderten PHP-Datei erfolgreich.

Validierung nach der Quelländerung: `php vendor/bin/pest tests/Unit/TranslationWorkbench --compact` – 211 Tests bestanden, 1.119 Assertions. Kein Browserlauf. Nächster Schritt bleibt der Abgleich der übrigen aktiven Package-Views; die neun nicht übernommenen Kandidaten sind getrennte Review-/Übersetzungsaufgaben.

## Nachtrag vom 14.10.: verbliebene Fundstellen der Entries-Views

Die noch nicht zugeordneten Aufrufe unter `resources/views/livewire/entries/` wurden geprüft: vier im Exportbericht, fünf in der Pipeline-Fortschrittsanzeige und einer in den Work-Filters. Acht sind feste Texte, zwei sind dynamische Aufrufe `__($badge['label'] ?? '')`. Letztere beziehen ihre Beschriftungen aus den Kontext-Badges der zuletzt bearbeiteten Übersetzungen und wurden als dynamische Kandidaten erfasst, nicht durch erfundene skalare Übersetzungen ersetzt.

Keiner dieser zehn Aufrufe löst aktuell direkt eine deutsche Übersetzung auf. Für „Context“ existiert „Kontext“ unter `admin.translation_list.modal_history.context`, dessen geprüfte Besitzer pending sind; deshalb keine automatische Quellumstellung. Die übrigen festen Texte ergaben in diesem Abgleich keine passenden gespeicherten de/en-Lang-Value-Kandidaten.

Der gezielte Foundation-Sync erstellte zehn Findings, zehn Beziehungen und neun pending-Key-Kandidaten; die zwei identischen dynamischen Aufrufe teilen einen Kandidaten. Es wurden 29 Timeline-Ereignisse geschrieben. Keine Änderungen an Anwendungscode, bestehenden Findings/Beziehungen, Übersetzungen oder Freigaben. Keine Veröffentlichung oder Stilllegung. Quellhashes und bestehende Datensätze wurden innerhalb einer SERIALIZABLE-Transaktion vor/nach der Erfassung verglichen.

Nachweise:

- `storage/app/private/translation-active-views-20261014-043522/` – Kandidatenabgleich und Quellhashes.
- `storage/app/private/translation-active-views-apply-20261014-043606/` – Sicherung und Ergebnis.
- `storage/app/private/translation-finding-drift-20261014-043618/` – unabhängiger vollständiger Nachscan.

Unter `entries/` bestehen nun 1.613 exakt aktive Aufrufe und der bekannte absichtlich inaktive leere Aufruf; keine weiteren unzugeordneten Aufrufe. Das bedeutet vollständige Finding-Erfassung in diesem View-Bereich, nicht vollständige Übersetzung. Insgesamt bleiben 13.238 unzugeordnete Aufrufe in anderen Bereichen. Als Nächstes sind die tatsächlich eingebundenen Raw-Data-Views von auskommentierten Vorschauen zu trennen und entsprechend abzugleichen. Kein neuer Pest-/Browserlauf, da kein Anwendungscode geändert wurde; die Datenänderung wurde über Transaktionsprüfungen und den unabhängigen Nachscan validiert.

## Nachtrag vom 14.10.: aktive Raw-Data-Einbindung und Fundstellen

Präzisierung zur bisherigen Einordnung: Der direkte Livewire-Einstieg `translation-workbench.raw-data` ist auskommentiert, **die Klasse `TranslationWorkbenchRawData` selbst wird aber weiterhin verwendet**, da die aktive `TranslationWorkbenchRawDataNew` von ihr erbt. Die fehlende Registrierung allein bedeutet deshalb nicht, dass deren Übersetzungsaufrufe stillgelegt sind.

Die aktive Einbindung führt über `table-panel` zu `timeline-chains/tabs`, `results-timeline-chains` und der Lazy-Preview. `graph-preview/tw-graph-data-driven` wird außerdem auf der TW-Graph-Datasets-Seite eingebunden. Zusatz-Callouts sind in Raw Data deaktiviert, können aber im gemeinsamen zweiten Verwendungskontext angezeigt werden. Die gemeinsame View wurde daher vollständig als Quelle berücksichtigt. Die auskommentierten Includes für canonical-root, canonical-roots, canonical-focus, samples und tw-graph-authoring wurden dagegen nicht wieder aktiviert oder deren offene Aufrufe in diesem Schritt synchronisiert.

176 offene Literal-Aufrufe in fünf Dateien wurden abgeglichen:

| Datei im Package | Aufrufe |
| --- | ---: |
| `src/Livewire/TranslationWorkbenchRawData.php` | 42 |
| `resources/views/livewire/raw-data/results-timeline-chains.blade.php` | 1 |
| `resources/views/livewire/raw-data/table-card.blade.php` | 2 |
| `resources/views/livewire/raw-data/timeline-chains/tabs.blade.php` | 2 |
| `resources/views/livewire/raw-data/timeline-chains/graph-preview/tw-graph-data-driven.blade.php` | 129 |

Keiner der geprüften Literal-Aufrufe löst aktuell direkt einen deutschen Text auf. Für 19 Stellen bestehen de/en-Lang-Value-Kandidaten unter anderen Keys; diese sind noch einzeln nach Freigabe und Kontext zu entscheiden. Insbesondere sind „Confidence“ → „Selbstvertrauen“ und „Literal“ → „Wörtlich“ keine passenden automatischen Übernahmen für technische Finding-Informationen. Weitere Kandidaten besitzen nur pending-Besitzer oder unterschiedliche deutsche Formulierungen. In diesem Schritt wurden deshalb keine Quelltexte ersetzt.

Der Foundation-Sync erfasste 176 Findings, 176 Beziehungen und 140 neue pending-Key-Kandidaten; 496 Timeline-Ereignisse wurden dokumentiert. Keine Freigabe, Veröffentlichung oder Stilllegung. Innerhalb der SERIALIZABLE-Transaktion wurden bestehende Finding-/Beziehungszeilen, Key-Namen/Freigaben, vier Werte-/Review-Tabellen und Quellhashes vor/nach der Erfassung auf unveränderten Inhalt geprüft.

Nachweise: `storage/app/private/translation-raw-active-20261014-043929/` (Audit), `translation-raw-active-apply-20261014-043949/` (Sicherung/Erfassung), `translation-finding-drift-20261014-044014/` (unabhängiger Gesamtscan). Der Nachscan bestätigt 5.917 exakt aktive und weiterhin 13.062 nicht zugeordnete Aufrufe; der bekannte leere Aufruf bleibt inaktiv. Diese Zahlen beschreiben Findings, nicht die Anzahl fehlender Übersetzungen. Kein neuer Pest-/Browserlauf, da kein Anwendungscode geändert wurde. Als Nächstes sind die 19 vorhandenen Übersetzungskandidaten kontextuell zu prüfen.

## Nachtrag vom 14.10.: Raw-Data-Keys und Nachvollziehbarkeit im Workbench-Bestand

Die acht vorherigen Umstellungen in `TranslationWorkbenchEntries.php` wurden unabhängig nachgeprüft: Zu jedem aktuellen Quellaufruf besteht ein aktives Finding mit aktiver Beziehung zu einem passenden freigegebenen Key. Die alten Literal-Findings sind obsolete; Timeline-Ereignisse dokumentieren sowohl Erfassung als auch Stilllegung. Audit: `storage/app/private/translation-provenance-check-20261014-044923/`. Die Zuordnung wurde damit nicht nur im Quellcode oder in einem Markdown-Bericht festgehalten.

Von den 19 Raw-Data-Kandidaten wurden neun Aufrufe auf bestehende freigegebene Keys umgestellt:

| Datei | Texte | Anzahl |
| --- | --- | ---: |
| `raw-data/timeline-chains/graph-preview/tw-graph-data-driven.blade.php` | Total, Outcome, Source (2), Status (2), Candidate, Timeline | 8 |
| `src/Livewire/TranslationWorkbenchRawData.php` | Namespace | 1 |

Verwendet wurden `ui.badge.total`, `ui.source.source`, `ui.state.status`, `ui.time.timeline` sowie die bereits vorhandenen Workbench-Keys für cleanup_history.outcome, findings_table.candidate und cleanup_history.namespace. Die de/en-Laufzeitwerte wurden vor der Änderung mit den erwarteten vorhandenen Übersetzungen verglichen. Keine neuen Übersetzungen, Keys oder Freigaben; keine Veröffentlichung.

Zehn Stellen bleiben offen: Events (2), #, Group, Context und Name haben im geprüften Kandidatenbestand nur pending-Besitzer. Suggested key besitzt den bereits dokumentierten Formulierungsunterschied. Literal → Wörtlich und Confidence → Selbstvertrauen passen nicht zum technischen Kontext. Bei Type wurde außerdem eine reale Bestandsabweichung gefunden: Der aktive de-Lang-Value lautet „Typ“, `lang/de/ui.php` enthält für `type` aber „Type“, und genau diesen Wert liefert der Translator. Die Vorprüfung stoppte deshalb vor jeder Änderung; Type wurde aus der Umsetzung herausgenommen. Diese Abweichung ist gesondert über den Veröffentlichungsweg zu klären, nicht durch eine stille Sprachdateiänderung.

Die beiden abgesicherten Umsetzungen verwenden Foundation-Syncer und TimelineRecorder. Sie erzeugten neun aktuelle Key-Findings mit aktiven Beziehungen, legten ausschließlich die neun ersetzten Literal-Findings samt Beziehungen still und behielten die historische Information. Zusätzlich enthält jedes aktuelle Finding ein `code_update_applied`-Ereignis mit altem und neuem Ausdruck, Quelle/Zeile, verwendetem Key sowie expliziten alten/neuen Finding-IDs. Der Kontext benennt den tatsächlichen Ursprung `translation-workbench:audited-source-reconciliation`; es wird weder ein UI-Klick noch ein Aufruf des CodeUpdateApplier vorgetäuscht. Die Quelländerung erfolgte in einem begrenzten Abgleichskript, die Daten- und Historienführung in der Workbench.

Nachweise:

- `storage/app/private/translation-raw-context-graph-20261014-045139/`
- `storage/app/private/translation-raw-context-namespace-20261014-045145/`
- `storage/app/private/translation-raw-context-verify-20261014-045229/`

Die separate lesende Nachprüfung bestätigte für alle neun Vorgänge Quell-Signatur, aktives Finding, freigegebenen Key, aktive Beziehung, stillgelegten Vorgänger und das direkt verknüpfende Timeline-Ereignis. Die Transaktionen sicherten unveränderte bestehende Key-Namen/Freigaben, Werte-/Review-Tabellen und nicht betroffene Findings. PHP bzw. kompiliertes Blade wurden syntaxgeprüft. Kein Browserlauf.

Tests nach der Umsetzung: `php vendor/bin/pest tests/Unit/TranslationWorkbench --compact` – 211 bestanden, 1.119 Assertions. Nächster gezielter Punkt ist die Abweichung `ui.type` zwischen gespeichertem deutschen Wert und Laufzeitausgabe.

## Nachtrag vom 14.10.: `ui.type` über den Workbench-Exporter wiederhergestellt

Die Abweichung wurde bis zum gespeicherten Wert und Git-Verlauf geprüft. Aktive Workbench-Lang-Values enthalten de „Typ“ und en „Type“; zwei Besitzer sind open/reviewed. Für diese Besitzer existieren keine gesonderten `key_values`, der vorhandene deutsche Wert stammt aus dem Lang-Import. Git-Commit `bac97754` vom 11.10.2026 änderte `lang/de/ui.php` von „Typ“ auf „Type“; der ältere Stand `53b3c654` enthält „Typ“. Welcher ausführende Prozess diese Quelländerung damals auslöste, ist damit nicht bewiesen.

Der gezielte Dry-Run des bestehenden `TranslationWorkbenchLangFileExporter` mit `locales: ['de']`, `keys: ['ui.type']` plante genau eine Änderung, keine neuen Werte, keine Löschungen und keine Konflikte. Dieser Export wurde anschließend mit `write: true` über denselben Workbench-Service ausgeführt. Ergebnis: ausschließlich der Wert `ui.type` in `lang/de/ui.php` lautet wieder „Typ“. Alle anderen Werte der Datei wurden vollständig verglichen und blieben identisch; Datenbankwerte, Freigaben und bestehende Findings blieben beim Export unverändert. Der Exporter schrieb das reguläre `lang_file_value_exported`-Ereignis 138928 mit Type → Typ und Bezug zum bestehenden Key. Es wurde keine neue Übersetzung erfunden oder freigegeben.

Danach wurde der einzelne Type-Aufruf in `raw-data/timeline-chains/graph-preview/tw-graph-data-driven.blade.php`, Zeile 1334, auf `__('ui.type')` umgestellt. Foundation-Sync und TimelineRecorder führten das neue aktive Finding und dessen Beziehung nach; der ersetzte Literal-Aufruf samt Beziehung wurde stillgelegt. Ein zusätzliches `code_update_applied`-Ereignis verbindet die konkreten alten/neuen Finding-IDs mit dem Key und beiden Ausdrücken. Die unabhängige Nachprüfung bestätigte diese Kette. Ein frischer PHP-Prozess liefert nun de „Typ“ und en „Type“.

Nachweise:

- `storage/app/private/translation-type-audit-20261014-045447/`
- `storage/app/private/translation-type-publish-20261014-045542/`
- `storage/app/private/translation-type-context-graph-20261014-045601/`
- `storage/app/private/translation-type-context-verify-20261014-045622/`

Damit sind zehn der 19 Raw-Data-Kandidaten übernommen; neun bleiben wegen pending-Status oder inhaltlich unpassender deutscher Werte offen. Keine allgemeine Veröffentlichung, kein globaler Export und keine pauschale Freigabe. PHP/Blade-Syntax und Daten-/Historienprüfungen erfolgreich; kein Browserlauf.

Tests nach Veröffentlichung und Quellumstellung: 211 Tests bestanden, 1.119 Assertions (`php vendor/bin/pest tests/Unit/TranslationWorkbench --compact`).

## Nachtrag vom 14.10.: sichtbare Workflow-Dokumentation erfasst

Die vier Tabs der aktiven Workflow-Seite (`legacy`, `workbench`, `target`, `write-contract`) wurden anhand ihrer tatsächlichen Includes geprüft. Die Legacy-Views sind weiterhin sichtbare Dokumentation des alten Ablaufs; ihre Erfassung aktiviert keine ausgemusterte Workbench-Funktion.

827 bisher nicht zugeordnete Literal-Aufrufe in 24 View-Dateien wurden abgeglichen. Sie enthalten 673 unterschiedliche Ausdrücke. Kein geprüfter Literal-Aufruf liefert unter seinem aktuellen Aufruf direkt einen deutschen Wert. Nur die zwei „Usage“-Aufrufe ergeben de/en-Lang-Value-Kandidaten (`admin.translation_list.modal.usage`, „Verwendung“); deren geprüfte Besitzer sind pending. Deshalb keine automatische Key-Ersetzung oder Freigabe. Die anderen Ausdrücke wurden nicht allein aufgrund ähnlicher Wörter vorhandenen Keys zugeordnet.

Der bestehende Foundation-Sync erstellte 827 Findings, 827 Beziehungen und 756 neue pending-Key-Kandidaten sowie 2.434 Timeline-Ereignisse. Die Zahl der Kandidaten ist nicht die Zahl unterschiedlicher Texte: Der Sync berücksichtigt auch Fundstellen-/Dateikontext und führt gleichlautende Texte nicht pauschal über alle Dateien zu einem Shared-Key zusammen. Keine neuen Übersetzungen, keine Veröffentlichung, keine Stilllegung und keine Quelländerung.

Die SERIALIZABLE-Transaktion prüfte bestehende Finding-/Beziehungszeilen, Key-Namen/Freigaben sowie die vier Werte-/Review-Tabellen auf unveränderten Inhalt. Alle neuen Keys wurden auf pending und fehlendes Freigabedatum geprüft. Quellhashes bestätigen unveränderte Views.

Nachweise:

- `storage/app/private/translation-workflow-views-20261014-045800/` – Kandidatenabgleich.
- `storage/app/private/translation-workflow-views-apply-20261014-045828/` – Sicherung und Erfassung.
- `storage/app/private/translation-finding-drift-20261014-045840/` – unabhängiger Nachscan.

Der Nachscan bestätigt alle 827 Workflow-View-Aufrufe als exakt aktiv zugeordnet. Insgesamt sind 6.744 Aufrufe exakt aktiv erfasst, 12.235 noch ohne entsprechendes Finding, ein bekannter leerer Aufruf bleibt inaktiv. Von den offenen Stellen liegen 11.802 im Pfadbereich `/tw-graph/`, 433 außerhalb; diese 433 enthalten unter anderem weitere TW-Graph-Hilfsklassen, stillgelegte Ansichten und noch aktive Workbench-Service-/Settings-Aufrufe. Als Nächstes werden die aktiven Service-/Settings-Aufrufe abgeglichen. Kein neuer Pest-/Browserlauf, da kein Anwendungscode geändert wurde; Prüfung durch Transaktionsvergleiche und unabhängigen Gesamtscan.

## Nachtrag vom 14.10.: aktive Services und Einstellungen

15 bislang nicht zugeordnete Aufrufe in acht Dateien wurden anhand ihrer aktiven Verwendung geprüft: DynamicEditor (1), EditorAccess (1), KeyReviewEditor (2), PipelineProcess (4), ScalarEditor (2), ScalarTransformWriter (1), TranslationWorkbenchSettings (3) sowie der geroutete Workflow-Seitentitel (1). Die Meldungen betreffen Editorvalidierung, Prozesszustände, Settings-Reset und den Seitentitel. Die Service-Aufrufe erfolgen aus der aktiven Workbench; Settings ist als Livewire-Komponente registriert und der Workflow-Seitentitel gehört zur aktiven Route.

Keiner der 15 Aufrufe liefert direkt einen deutschen Text. Der Abgleich mit vorhandenen de/en-Lang-Values ergab keine passenden deutschen Kandidaten. Es wurden daher keine neuen Übersetzungen formuliert und keine Quelltexte umgestellt.

Der Foundation-Sync erstellte 15 Findings, 15 Beziehungen, 15 pending-Key-Kandidaten und 53 Timeline-Ereignisse. Bestehende Key-Namen/Freigaben, sämtliche Werte-/Review-Tabellen sowie vorhandene Findings/Beziehungen blieben gemäß Transaktionsvergleich unverändert. Quellhashes bestätigen unveränderte Dateien; keine Stilllegung, Veröffentlichung oder Freigabe.

Nachweise:

- `storage/app/private/translation-service-settings-20261014-045956/`
- `storage/app/private/translation-service-settings-apply-20261014-050018/`
- `storage/app/private/translation-finding-drift-20261014-050026/`

Der unabhängige Gesamtscan bestätigt nun 6.759 exakt aktive Aufrufe und 12.220 weiterhin nicht zugeordnete Aufrufe; der bekannte leere Aufruf bleibt inaktiv. Die offenen Stellen liegen überwiegend in TW-Graph. Die verbleibenden 418 außerhalb des Pfadsegments `/tw-graph/` enthalten ebenfalls graphbezogene PHP-/Common-Komponenten sowie ausgemusterte Ansichten; diese sind nicht mit 418 fehlenden Workbench-Übersetzungen gleichzusetzen. Als Nächstes sind die aktiven Dataset-/Graph-Hilfsaufrufe separat zu prüfen. Kein neuer Pest-/Browserlauf bei diesem reinen Bestandsabgleich; Validierung durch Transaktionsprüfungen und unabhängigen Nachscan.

## Nachtrag vom 14.10.: aktive Dataset-/Graph-Hilfsaufrufe

32 bisher nicht zugeordnete Aufrufe in fünf aktiven Dateien wurden geprüft: `SelectsTimelineChainDataset` (2), `TwGraphDataDrivenDatasets` (16), `TwGraphDatasetDetails` (2), `DatasetLabelContext` (6) und `TestFailureDetails` (6). Die ersten Komponenten werden auf Dataset-/Raw-Data-Seiten verwendet; `DatasetLabelContext` liefert die Rechtsklick-Menüs der Graph-Labels, `TestFailureDetails` wird in Diagnostics und Fail Summary eingebunden.

Der Kandidatenabgleich fand 27 unterschiedliche Ausdrücke, keine direkten deutschen Literal-Auflösungen und einen passenden vorhandenen Übersetzungskandidaten: Overview → `ui.title.overview` → Übersicht. Der initiale Foundation-Sync erfasste 32 Findings, 32 Beziehungen und 27 pending-Key-Kandidaten mit 96 Timeline-Ereignissen. Vorhandene Werte/Freigaben und vorhandene Findings/Beziehungen blieben unverändert.

Anschließend wurde ausschließlich `src/Support/DatasetLabelContext.php`, Zeile 13, von `__('Overview')` auf `__('ui.title.overview')` umgestellt. Vor der Änderung wurden der vorhandene aktive/reviewed-Besitzer und die de/en-Laufzeitwerte Übersicht/Overview geprüft. Der aktuelle Key-Aufruf ist aktiv zugeordnet; der ersetzte Literal-Aufruf samt Beziehung ist stillgelegt, nicht gelöscht. Das `code_update_applied`-Ereignis enthält beide Finding-IDs, Quelle, Ausdrücke und verwendeten Key. Der initial angelegte ungeprüfte Literal-Kandidat wurde weder freigegeben noch als Ersatz für den vorhandenen freigegebenen Key verwendet. Keine Änderung von Übersetzungswerten, kein Export und keine Änderung am Menüverhalten.

Nachweise:

- `storage/app/private/translation-dataset-helpers-20261014-050142/`
- `storage/app/private/translation-dataset-helpers-apply-20261014-050202/`
- `storage/app/private/translation-dataset-context-graph-20261014-050240/`
- `storage/app/private/translation-dataset-context-verify-20261014-050302/`
- `storage/app/private/translation-finding-drift-20261014-050302/`

Transaktionsprüfungen bestätigten unveränderte Bestandswerte und Freigaben. Die separate Nachprüfung bestätigte die direkte historische Zuordnung sowie das aktuelle Finding mit aktivem freigegebenem Besitzer. Der Gesamtscan ergibt 6.791 exakt aktive Aufrufe, 12.188 weiterhin nicht zugeordnete Aufrufe und den bekannten inaktiven leeren Aufruf. Die offenen Stellen bestehen überwiegend aus TW-Graph-Dokumentation; aktive Common-/Overview-Helfer sind noch getrennt von ausgemusterten Ansichten zu erfassen. PHP-Syntaxprüfung erfolgreich; kein Browserlauf.

Nach der Quelländerung bestanden alle 211 TranslationWorkbench-Tests mit 1.119 Assertions, einschließlich der Dataset-Kontext-Tests.

## Abschluss des zusammenhängenden Bestandsabgleichs vom 14.10.2026

Der zuvor in kleinen Abschnitten durchgeführte Abgleich wurde für den restlichen Bestand vollständig abgearbeitet. Der Abschluss bezieht sich auf die aktuellen Übersetzungsaufrufe in den konfigurierten Scanner-Pfaden, ohne Kommentare, den bekannten leeren Aufruf und die unten ausdrücklich ausgesparten Altansichten. Er behauptet nicht, dass jeder erfasste Beispiel-/Test-View gerade über die Navigation sichtbar ist. Nicht mit Übersetzungsfunktionen umschlossene Texte sind nicht Teil dieser Scanner-Auswertung.

### Erfassung und bestehende Übersetzungen

12.033 weitere Aufrufe in 308 Dateien wurden über den Foundation-Syncer erfasst: 12.033 Findings, 12.033 Beziehungen, 7.961 neue pending-Key-Kandidaten und 32.336 Timeline-Ereignisse. Gleichlautende Ausdrücke werden nach den bestehenden Kontextregeln behandelt, nicht pauschal zu Shared-Keys zusammengezogen. Die SERIALIZABLE-Transaktion prüfte bestehende Finding-/Beziehungszeilen, Key-Namen/Freigaben, Werte-/Review-Tabellen sowie die Quellhashes auf unveränderten Inhalt.

391 kontextuell geprüfte Aufrufe in 43 Dateien wurden anschließend auf bereits vorhandene aktive/freigegebene Keys umgestellt:

| Key | Aufrufe |
| --- | ---: |
| `ui.type` | 365 |
| `ui.title.overview` | 9 |
| `ui.state.status` | 9 |
| `ui.source.source` | 4 |
| `ui.badge.total` | 2 |
| `ui.button.copy.copy` | 1 |
| `ui.nouns.summary` | 1 |

Die Type-Aufrufe sind Tabellenüberschriften für Prop-/Datentypen; die übrigen Aufrufe wurden im konkreten Kontext geprüft. Deutsche und englische Laufzeitwerte wurden vor der Umstellung mit den gespeicherten Kandidaten verglichen. Keine neuen Übersetzungen oder Freigaben und kein Export in diesem Abschlussabschnitt. Jede Änderung besitzt ein aktuelles aktives Finding mit freigegebenem Besitzer. Alte Findings/Beziehungen wurden mit Historie stillgelegt; direkte `code_update_applied`-Ereignisse enthalten alte/neue Finding-ID, Key, Quelle und Ausdrücke. Bestehende Werte, Freigaben und nicht betroffene Findings/Beziehungen blieben unverändert.

157 weitere Aufrufe besitzen zwar de/en-Textkandidaten, wurden aber nicht umgestellt: 154 wegen fehlender aktiver Freigabe und drei wegen falscher Bedeutung. Zweimal „View“ bezeichnet eine View-Datei, während der vorhandene Wert „Anzeigen“ lautet. „Actual“ bezeichnet einen tatsächlichen Testwert; „Aktuell“ wäre irreführend. Die vollständige Liste mit Fundstelle und Entscheidungsgrund steht im Audit `deferred-candidates.json`.

### Fünf ältere Zuordnungsabweichungen ebenfalls korrigiert

Die abschließende Prüfung aller aktiven Quellaufrufe fand fünf ältere Abweichungen zwischen explizitem Quell-Key und bereits freigegebenem Workbench-Besitzer. Der bestehende CodeUpdateApplier bestätigte alle fünf als anwendbar. Alte/neue de/en-Laufzeitwerte sind jeweils identisch:

| Datei | Bisheriger Key | Freigegebener Ziel-Key |
| --- | --- | --- |
| `resources/views/components/admin/partials/translation-sub-languages/⚡modal-edit.blade.php` | `admin.translation_list.modal_edit.open_next_editable_entry` | `ui.button.open.open_next_editable_entry` |
| `resources/views/components/admin/partials/translation-usage-audit/modal/⚡translation-values.blade.php` | `admin.app_settings.table_icon_registry.status` | `ui.state.status` |
| `resources/views/components/ui/button/confirm.blade.php` | `pages.settings.two_factor_setup_modal.confirm` | `ui.button.confirm.confirm` |
| `resources/views/components/ui/button/create.blade.php` | `admin.roles.actions.create` | `ui.button.create.create` |
| `resources/views/components/ui/button/edit.blade.php` | `admin.translation_list.modal.edit` | `ui.button.edit.edit` |

Die fünf Umstellungen wurden tatsächlich durch `TranslationWorkbenchCodeUpdateApplier` ausgeführt, einschließlich seiner regulären Code-Update-Timeline-Ereignisse. Ein gezielter Foundation-Sync erfasste die aktuellen Aufrufe; die Vorgänger wurden per `finding_superseded` mit explizitem Nachfolgerbezug stillgelegt. Keine neue Review-Entscheidung, keine Übersetzungsänderung und keine Löschung historischer Daten.

### Abschließendes Prüfergebnis

| Prüfung | Ergebnis |
| --- | ---: |
| Aktuelle Aufrufe im bearbeiteten Bereich | 18.824 |
| Davon exakt aktive Findings | 18.824 |
| Fehlende Findings oder Beziehungen | 0 |
| Abweichende aktive Besitzer bei expliziten Keys | 0 |
| Direkt unter dem aktuellen Aufruf auflösbare deutsche Texte | 2.206 |
| Noch nicht direkt auflösbare deutsche Texte | 16.564 |
| Dynamische Aufrufe mit gesonderter Auflösung/Prüfung | 54 |

16.564 bezeichnet Aufrufstellen, nicht 16.564 neu zu schreibende Übersetzungen; darin liegen 6.639 unterschiedliche Roh-Ausdrücke, einschließlich wiederholter Dokumentations-/Beispieltexte und gegebenenfalls bewusst unveränderter technischer Bezeichnungen. Diese Stellen sind **im Workbench-Bestand erfasst**, müssen aber in dessen regulärem Übersetzungs-/Review-Workflow weiterbearbeitet werden. Das ist kein fehlender Schritt der Finding-Erfassung. Dynamische Aufrufe werden nicht als pauschal fehlende skalare Übersetzungen bewertet.

Bewusst nicht neu erfasst oder reaktiviert wurden 155 Aufrufe in sechs ausgesparten Altquellen:

| Altquelle | Aufrufe |
| --- | ---: |
| `TranslationWorkbenchOldEntries.php` | 79 |
| `raw-data/timeline-chains/canonical-focus.blade.php` | 22 |
| `raw-data/timeline-chains/samples.blade.php` | 19 |
| `raw-data/timeline-chains/graph-preview/tw-graph-authoring.blade.php` | 16 |
| `raw-data/timeline-chains/canonical-roots.blade.php` | 10 |
| `raw-data/timeline-chains/canonical-root.blade.php` | 9 |

Der globale Rohscan meldet diese weiterhin als nicht zugeordnet; die Scanner-Konfiguration wurde dafür nicht stillschweigend geändert. Der bekannte leere Aufruf bleibt absichtlich inaktiv. Die 155 Altstellen und der leere Aufruf sind vollständig erklärt und von den 18.824 bearbeiteten Aufrufen getrennt.

### Sicherungen und reproduzierbare Nachweise

Alle Pfade liegen unter `storage/app/private/`:

- `translation-completion-20261014-050640/`: vollständiger Restbestand, ausgesparte Stellen, Kandidaten, getroffene Zuordnungen, Zurückstellungen und Quellhashes.
- `translation-completion-register-20261014-050734/`: Sicherung und Erfassung der 12.033 Aufrufe.
- `translation-completion-reuse-20261014-050942/`: Originaldateien, Datenbank-Snapshots und 391 direkte Änderungsketten.
- `translation-completion-five-20261014-051206/` und `translation-completion-five-apply-20261014-051332/`: Trockenlauf, Sprachwertvergleich und Anwendung der fünf bestehenden Review-Zuordnungen.
- `translation-completion-verify-20261014-051400/`: unabhängige Prüfung aller 18.824 Aufrufe, keine Zuordnungsfehler; vollständige Liste noch nicht direkt auflösbarer bzw. dynamischer Aufrufe mit Finding-IDs.
- `translation-finding-drift-20261014-051400/`: vollständiger Scanner-Abgleich mit genau 155 ausgesparten Aufrufen und dem einen leeren inaktiven Aufruf.

Alle geänderten PHP-Dateien bzw. kompilierten Blade-Dateien wurden syntaxgeprüft. Kein Browserdurchlauf; die Nachweise beziehen sich auf Quellcode, Translator-Laufzeitauflösung und Workbench-Datenbestand.

### Abschließende Tests und Abschlussgrenze

Der gemeinsame Testlauf für `tests/Unit/TranslationWorkbench` und `tests/Unit/TwGraph` führte 2.166 Tests aus: 2.161 bestanden zunächst, fünf scheiterten an veralteten Test-Erwartungen. Drei waren Quelltextvergleiche (Type, Diagnostics-Spalten und der bereits vorhandene kombinierte DEV-/Zusatz-Callout-Schalter); zwei erwarteten bisher fest englische Source-/Overview-Beschriftungen. Die Tests wurden auf die nun verwendeten Übersetzungskeys bzw. den bestehenden DEV-Vertrag angepasst, ohne die getesteten Layout-/Diagnosefunktionen zu ändern.

Die vier betroffenen Dateien `CalculatedLengthsTest.php`, `DataDrivenDebugBoundsViewTest.php`, `DocumentationLazyLoadingTest.php` und `TwGraphDiagnosticsViewTest.php` wurden danach vollständig erneut ausgeführt: **60 Tests bestanden, 1.286 Assertions**, keine verbleibenden Fehler. Zusätzlich bestanden nach den fünf älteren Key-Korrekturen erneut alle **211 TranslationWorkbench-Tests mit 1.119 Assertions**. Die übrigen Tests des breiten Laufs waren bereits erfolgreich; der komplette 2.166-Test-Lauf wurde nach den reinen Erwartungskorrekturen nicht nochmals wiederholt. Originalausgabe und grüner Nachlauf sind als `tests-initial.json` und `tests-retest.json` im abschließenden Verify-Verzeichnis gesichert. Der Git-Whitespace-Check für die bearbeiteten Dateien ist ebenfalls sauber.

**Dieser Bestands-/Zuordnungsabgleich ist abgeschlossen.** Es verbleibt kein weiterer technischer Erfassungsabschnitt innerhalb der beschriebenen Abschlussgrenze. Fachliche Übersetzung, Review der pending-Kandidaten und dynamischer Werte sowie eine mögliche spätere Reaktivierung ausdrücklich ausgesparter Altansichten bleiben separate reguläre Workbench-Aufgaben. Keine pauschalen Freigaben und kein Commit/Push in diesem Abschnitt.
