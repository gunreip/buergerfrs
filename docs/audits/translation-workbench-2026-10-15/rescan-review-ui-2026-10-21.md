# Lesende Workbench-Ansicht für isolierte Scan-Prüffälle – 21.10.2026

## Aufruf

Translation Workbench → Workbench: oberhalb der vorhandenen aktuellen
Scan-Vorschau erscheint das Callout **Isolated rescan review**. Das Accordion
**Inspect preserved findings and scan questions** öffnet die Dateiliste.

Die Ansicht ist ausdrücklich lesend. Die reguläre Datenbank ist nicht
umgeschaltet. Finding-IDs gehören zur isolierten Generation und werden nicht
als Links auf Datensätze der bisherigen Workbench verwendet.

## Inhalt

- Generation, ursprünglicher Scan und Exportzeitpunkt.
- 18.878 bestätigte und 1.456 vorläufig erhaltene Findings.
- 39 Dateien mit insgesamt 72 offenen Erfassungsfragen.
- Suche nach Dateipfad und Flux-Pagination.
- Je Datei: Prüfgründe, Roh-Ausdruck, Zeile/Spalte und aufklappbare erhaltene IDs.
- Verlagerungskandidaten können mit Ausgangsfinding und Zielstellen dargestellt werden.

Die 1.456 erhaltenen Findings sind nicht als 1.456 Fehler bezeichnet. Sie liegen
in Dateien mit ungeklärten Erfassungen, deren Bestand vorsichtshalber erhalten
wurde. Die Ansicht erlaubt noch keine Freigabe, Stilllegung oder Key-Zuordnung.

## Datenweg

Der lesende vollständige Plan wurde mit dem aktuellen Prüfcode erneut auf einer
isolierten Datenbank erstellt: 1.691 Assertions bestanden. Der Bericht enthält
jetzt den SHA256 des vollständigen Plans. `export-rescan-review.php` prüft
Erfolgsstatus, unveränderte Datenbank, Generation und Planhash vor dem Export.

Ausgangsbericht:
`storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_871e5a22182e/result.json`.

Export:
`storage/app/private/workbench-previews/rescan-review-20261021-004802-5875d3d15543`.

Der Webprozess liest den Manifest-Export und nur die ausgewählte Detaildatei.
Manifest und Details werden per Hash kontrolliert; eine fremde Generation wird
abgewiesen. Der aktuelle Zeiger wird atomar ersetzt. Dateirechte entsprechen der
vorhandenen Scan-Vorschau (private Storage-Ablage, webprozesslesbar). Die privaten
Datenbanksicherungen selbst werden nicht für den Webprozess geöffnet.

Die Anzeige ist eine datierte Momentaufnahme des gesicherten Quellstands, kein
Live-Scan des inzwischen weiterentwickelten Repositorys. Ein Seitenaufruf führt
keinen Scan aus und baut keine Verbindung zur isolierten PostgreSQL-Datenbank auf.

## Validierung

### Lesende Beobachtung der Datenbank-Optionsquellen

Unter **Observed database option values** sind drei separat aufklappbare
Beobachtungen sichtbar: Dokumentarten (11), Dokumentkategorien (6), Rollenkategorien
(1). Das sind Rohwerte aus Anwendungstabellen, keine importierten Findings der
isolierten Generation und keine DatabaseValue-Entscheidungen.

`observe-runtime-option-values.php` liest in einer PostgreSQL-Transaktion mit
`REPEATABLE READ READ ONLY` ausschließlich:

- `person_document_types.code,label`: aktive Einträge, geordnet nach sort_order/label/code.
- `person_documents.category`: unterschiedliche, nicht NULL/leere Werte.
- `roles.category`: unterschiedliche Kategorien zuweisbarer Rollen, NULL/leer erhalten.

Keine Model-/Provider-/Collector-Aufrufe. Kein Übersetzen oder Headline-Erzeugen.
Kein Rückgriff auf Code-Defaults und keine Erfassung noch ungespeicherter UI-Werte.
Der Bericht belegt deshalb nur die beobachteten DB-Werte zu seinem Zeitpunkt,
nicht eine vollständige effektive Optionsliste. Eine fehlende Tabelle oder
fehlgeschlagene Abfrage bricht ab und ersetzt den letzten Export nicht durch
eine vermeintlich leere Liste. Erfolgreich leer ist `observed_empty` und bewirkt
keine Stilllegung.

Die private Ablage `workbench-previews/runtime-options.json` zeigt atomar auf den
datierenden Export. SHA256 und die unveränderten Quell-Definitionen werden beim
Lesen geprüft. Die UI führt keine Datenbankabfrage aus. Sie kennzeichnet die
Anwendungsdaten ausdrücklich getrennt von der isolierten Finding-Generation.
Vor einer späteren Entscheidung ist eine erneute Beobachtung erforderlich;
eine Momentaufnahme beweist nicht die zwischenzeitliche Unverändertheit der DB.

Validierung: 17 Tests / 60 Assertions (Rohwerte, Auswahl, NULL/leer, Fehler statt
Leer-Erfolg, fehlende Transaktion, Exportmanipulation, Quelländerung, sichere
Darstellung und bestehende Vorschauen). Echter PostgreSQL-Leselauf erfolgreich:
11/6/1 Werte, READ ONLY auf Server-Seite geprüft. Keine DB-Schreiboperation,
keine UI-Entscheidung und keine manuelle Browserprüfung.

### Listennachweise an Fundstellen

14 Optionsaufrufe zeigen jetzt ein Accordion **Default list evidence** mit
Schlüssel, Default-Wert, Definitionsdatei/Zeile und gegebenenfalls zusätzlichem
Konstantennachweis. Die 14 DB-/laufzeitabhängigen Aufrufe erhalten keine feste
Liste. Die Anzeige bleibt lesend; keine Freigabe oder DatabaseValue-Entscheidung.

`option-list-review-bindings.php` hält die ausdrücklich geprüften Beziehungen
zwischen exaktem View-Pfad, vollständigem Optionsgruppen-Key und Klasse/Property
bzw. Config-Pfad fest. Keine Suffix- oder Textähnlichkeitszuordnung. Der Binder
vergleicht Fundstellen-Hash, Ausdruck an der gespeicherten Position, alle neun
Dateien der geprüften Produzentenkette und die Hashes der Listendefinition sowie
Konstanten. Bei Änderungen zeigt die UI einen Prüfhinweis ohne Default-Werte.
Die konservative Bindung an alle neun Dateien kann auch bei einer anderweitigen
Änderung eine erneute Inventur verlangen; sie behauptet keine Laufzeitvollständigkeit.

Die exportierten Details enthalten die Nachweise selbst und sind wie bisher
hashgebunden. Der Manifest-Export nennt zusätzlich die Hashes von Inventur und
Zuordnungsdatei. Es handelt sich um eine datierte Momentaufnahme, nicht um eine
Live-Prüfung bei jedem Seitenaufruf. Unverändert 59 offene Fragen / 32 Dateien.

29 Tests / 84 Assertions bestanden: Zuordnung, abweichende Quellen/Konstanten,
mehrdeutige Listen, falsche Quellpositionen, escaped Ausgabe und bestehende
Vorschaufunktionen. Tatsächlicher Export: 14 zugeordnete / 14 ohne feste Liste.
Keine manuelle Browserprüfung. Keine produktiven Datenänderungen.

### Kontext der 28 Optionsaufrufe

Alle 28 `special_call_requires_context`-Fälle im gesicherten Quellstand sind
Aufrufe von `TranslationWorkbenchOption::label`. Die Erfassung trennt nun
`owner_key`, `value_expression`, `fallback_expression` und `locale_expression`
unter `option_source`. Argumenttexte stammen aus den AST-Positionen des
hashgeprüften Originalausdrucks; keine Ausführung oder Auswertung von Produzenten.
Ein statisch geschriebener Fallback wird separat als `fallback_literal` erfasst,
niemals als `raw_literal` des Findings. Unbekannte/namentliche/entpackte
Argumentformen werden nicht anhand ihrer Position interpretiert.

In der Dateiprüfung zeigt die UI diese Angaben getrennt und escaped. Der
Gruppen-Key ist kein skalarer Übersetzungskey: `label()` ergänzt `.options.`
und den kodierten Optionswert. Alle 28 realen Aufrufe verwenden dynamische
Optionswerte. Deshalb bleiben sie zur Prüfung offen, bis die Herkunft und die
zulässigen Werte belegt sind. Die Anzahl bleibt bei 59 Fragen / 32 Dateien.

Validierung: 16 Pest-Tests / 62 Assertions, 42 bestehende Capture-Prüfungen und
1.698 Assertions beim vollständigen isolierten Abgleich bestanden. Alle 28
realen Optionsaufrufe enthalten die getrennten Angaben; `found_translation_key`
und `raw_literal` bleiben NULL. Produktive Daten unverändert. Keine manuelle
Browserprüfung.

Bericht: `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_fdf21ff5f73e/result.json`.
UI-Export: `storage/app/private/workbench-previews/rescan-review-20261021-041831-443bddd9c49b`.

### Scannerkorrektur: JavaScript-Serialisierung

Die ursprünglichen 72 Fragen enthielten 13 reine `@js($variable)`-Aufrufe.
Diese serialisieren Werte und sind keine Übersetzungsaufrufe. Der entsprechende
Scanner-Ausdruck wurde entfernt. Verschachtelte echte Aufrufe wie
`@js(__('Copy label'))`, `@js(trans('Save label'))` und `@js(__($labelKey))`
bleiben erfasst und sind durch einen Regressionstest abgesichert.

Der erneute Abgleich desselben gesicherten Quellstands ergibt 32 Prüfdateien,
18.914 bestätigte und 1.420 vorläufig erhaltene Findings. Die Datenbank blieb
unverändert; 1.698 Assertions bestanden. Scanner-Tests: 7 Tests / 40 Assertions.
Die oben dokumentierten ursprünglichen Zahlen bleiben als Verlauf erhalten.

Neuer Bericht: `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_3aef012b0aa4/result.json`.
Aktueller UI-Export: `storage/app/private/workbench-previews/rescan-review-20261021-005625-27ab7c2c9291`.
Keine automatische Auflösung dynamischer Übersetzungen, Optionsaufrufe oder
keyähnlicher Literale; keine Änderung produktiver Findings.

12 Pest-Tests / 32 Assertions bestanden (neue Export-/Rendering-Prüfungen und
bestehende Scan-Vorschau). Abgedeckt sind Generationstrennung, manipulierte
Detailnachweise, unbekannte IDs/Pfadversuche, escaped Roh-Ausdrücke, Suche und
Zurücksetzen der Auswahl. Alle 39 tatsächlichen Exportdetails wurden zusätzlich
per Hash geprüft. Pint und `git diff --check` bestanden.

Keine manuelle Browserprüfung durchgeführt. Der nächste Schritt ist die fachliche
Begutachtung dieser Ansicht und anschließend der kontrollierte Anschluss von
Prüfentscheidungen und regulärem Scannerbetrieb; keine automatische Bearbeitung
des bisher produktiven Datenbestands.
