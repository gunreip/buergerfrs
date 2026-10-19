# Verbleibende unbenannte Kandidaten: ursprüngliche Quellstellen

## Prüfung der 143 Zusatzlinks

Die historischen `key_candidate_discovered`-Ereignisse liefern für jeden Key seine ursprüngliche Datei. Alle 17 Dateien existieren noch; das allein beweist aber keine aktive Fundstelle.

| Quellenzustand | Keys / Zusatzlinks |
| --- | ---: |
| Ursprungsdatei ausdrücklich vom Scan ausgeschlossen (`livewire/old`) | 50 |
| Ursprünglicher Literal-Ausdruck in der damaligen Datei nicht mehr vorhanden | 85 |
| Literal-Ausdruck weiterhin vorhanden | 8 |

Die 85 fehlenden Ausdrücke sind nicht automatisch als gelöscht einzustufen. Beispielsweise wurden aus `entries/findings-table.blade.php` viele Inhalte in Partial-Views ausgegliedert. 74 dieser Kandidaten stammen aus dieser Datei. Die heutige Fundstellen-/Key-Zuordnung ist daher getrennt vom historischen Kandidaten zu prüfen.

## Durchgeführte Reparaturen

Sieben vorhandene Literal-Fundstellen waren eindeutig verschoben, nicht verschwunden:

| Finding | Vorherige Zeile | Aktuelle Zeile |
| --- | ---: | ---: |
| #9010 | 152 | 131 |
| #9018 | 248 | 227 |
| #5407 | 60 | 39 |
| #5409 | 71 | 50 |
| #5415 | 103 | 82 |
| #5416 | 105 | 84 |
| #5424 | 137 | 116 |

Der vorhandene `TranslationWorkbenchSourceRepair` hat ausschließlich diese sieben Positionen aktualisiert und protokolliert. Danach erfüllten die sieben fremden Links #4206, #4356, #4212, #4362, #4371, #4363 und #4354 die schon zuvor verwendeten strengen Reparaturkriterien und wurden ebenfalls protokolliert archiviert.

Die 50 Keys aus dem ausgeschlossenen Alt-Workbench-Verzeichnis waren sämtlich pending, ohne TranslationKey, Reviews oder eigene scalar/dynamic Key-Werte. Jeder hatte nur noch eine aktive, falsche Verknüpfung zu einem heutigen Finding in einer anderen Datei. Das heutige Finding und sein passender Key wurden vor der Reparatur im Code bestätigt. Die 50 falschen Links und anschließend die 50 nun unbenutzten Keys erhielten `obsolete`, mit Grund `excluded_historical_source` und insgesamt 100 Timeline-Einträgen. Keine Weiterleitung oder Zusammenführung mit dem heutigen Key angelegt.

Vorher-/Nachher-Prüfsummen bestätigen für die Link-/Altbestand-Reparaturen unveränderte Findings, SourceFiles, Reviews, Übersetzungswerte und unbeteiligte Keys/Links. Quelldateien wurden nicht verändert. Die separat genannten sieben Positionskorrekturen wurden bewusst über den bestehenden Reparaturdienst vorgenommen.

## Offen

- 83 unbenannte Mehrfachzuordnungen mit 86 Zusatzlinks.
- 85 historische Ursprünge ohne aktuellen Ausdruck in derselben Datei: 80 betroffene Keys hatten im geprüften Bestand nur den fremden Link, fünf noch eine weitere aktive Verknüpfung. Diese weitere Verknüpfung muss vor einer Key-Archivierung geprüft werden. Aus fehlendem Ausdruck allein folgt keine Freigabe zur Archivierung.
- Key #3863: zwei aktuelle `Key`-Literale in `entries/modal-dynamic-source-link-confirm.blade.php`, Zeilen 90 und 134; Findings #3243/#3246 werden vom Matcher als mehrdeutig ausgewiesen. Keine Zuordnung allein nach Reihenfolge vorgenommen. Der zusätzliche Link zu Finding #482 bleibt vorerst bestehen.
- Zusätzlich unverändert 31 benannte Mehrfachzuordnungen gleichen Schlüsseltexts und ein Fall mit verschiedenen Schlüsseltexten. Insgesamt verbleiben 115 Findings mit Mehrfachzuordnungen.

## Nachweise

- [Quellenprüfung, Reparaturergebnisse, Timeline-IDs und Prüfsummen](remaining-unkeyed-origins-2026-10-17.json).
- [Begrenzter Positionsplan](refine-unkeyed-witness-positions-2026-10-17.php).
- [Geprüfte Alt-Workbench-Ursprünge](excluded-unkeyed-origins-2026-10-17.json).
- [Transaktionale Altbestand-Archivierung](retire-excluded-unkeyed-origins-2026-10-17.php): read-only standardmäßig, Apply nur mit frisch identischem Plan; erneuter Plan nach Durchführung enthält null Aktionen.

Validierung: 17 gezielte Pest-Regressionstests bestanden, 223 Assertions; PHP-Syntaxprüfungen bestanden. Keine neue Laufzeitlogik und kein vollständiger Testlauf.
