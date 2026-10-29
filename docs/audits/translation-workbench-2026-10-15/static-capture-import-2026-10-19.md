# Probe-Erfassung im neuen Schema

Stand: 19. Oktober 2026. Ergebnis: `isolated_capture_verified`.

Die geprüften statischen Kandidaten wurden in einer **neuen isolierten Testdatenbank** mit dem vorhandenen PostgreSQL-Baselineschema erfasst. Produktive Datenbank, restauriertes Altarchiv und Nachweis-Datenbank wurden nicht beschrieben. Es wurde keine produktive Generation aktiviert.

## Vollständige Kandidatenbilanz

| Ergebnis | Anzahl |
| --- | ---: |
| Neue Finding-Datensätze im Testschema | 19.940 |
| Davon Literal-Kandidaten | 17.733 |
| Davon Keys mit exaktem englischem Sprachdatei-Beleg | 2.207 |
| Ausgeschlossen: Kommentare und PHP-Stringinhalte | 108 |
| Offen: dynamische/nichtskalare Argumente | 34 |
| Offen: spezielle Aufrufe mit weiterem Kontextbedarf | 28 |
| Offen: Key-ähnliche Argumente ohne Sprachdatei-Beleg | 10 |
| Alle Kandidaten | 20.120 |

Die neuen Findings haben im **isolierten** Schema den Status `active`, weil sie im gesicherten Quellstand erfasst wurden. Dies bedeutet weder Freigabe einer Übersetzung noch bestätigte Schlüsselzuordnung. Alle 1.684 gescannten Quelldateien, auch solche ohne Treffer, sind in der Scan-Herkunft enthalten. Es existiert genau ein abgeschlossener Erfassungslauf. Die begrenzte Discovery-Vollständigkeit bleibt ausdrücklich im Scope vermerkt.

## IDs und Herkunft

Die Datenbank vergibt neue Finding-IDs. `capture_outcomes` verknüpft jeden Kandidaten über Capture-ID und Manifest-SHA mit entweder einer neuen Finding-ID, einem Ausschluss oder einer offenen Prüfung. Alte Finding-/Key-IDs werden nicht übernommen. Gleiche Literaltexte an unterschiedlichen Positionen bleiben getrennte Findings.

Die fünf Rohfelder werden unverändert aus der geprüften Erfassung übernommen; Quelle, Zeile, Byte-Spalte, Offset, Dateihash, erfasster Key und Textvorschlagsherkunft bleiben nachvollziehbar. Ein bereits im Code stehender Key wird in der Provenienz festgehalten, aber noch keinem Key-Datensatz zugeordnet. Vor dem Import wurden sämtliche im Erfassungsmanifest referenzierten Quell- und Sprachdateien erneut gegen die Sicherung geprüft.

Der Lauf erstellt keine Keys, Key-Finding-Verknüpfungen, Übersetzungsrevisionen, Reviews, Veröffentlichungen oder alten/neuen Timeline-Events. Scan-Datensatz und unveränderliches Importprotokoll dokumentieren die technische Probe. Ein Benutzer-Akteur wird dafür nicht erfunden; die fachliche Arbeitsdokumentation mit tatsächlichem Akteur gehört in die spätere Service-/UI-Anbindung.

## Ausgeführte Integrationsprüfungen

- Alle 19.940 erfassten Findings aus der Datenbank zurückgelesen: fünf Rohfelder sowie Pfad, Zeile und Spalte stimmen mit den Kandidaten überein.
- Jeder der 20.120 Kandidaten hat genau ein Ergebnis; keine zusätzlichen Ergebniszeilen.
- Identischer Import erneut aufgerufen: vorhandenes Ergebnis zurückgegeben, weiterhin ein Scan und unveränderte Finding-/Scan-Verknüpfungszahlen.
- Änderung eines gespeicherten SuggestedKeys vom Datenbanktrigger abgewiesen.
- Zusätzlicher fehlerhafter Lauf mit doppelter Capture-ID nach einem ersten Insert: gesamte Transaktion zurückgerollt, keine zusätzlichen Findings, Scans oder Importdatensätze.
- Sieben nachgelagerte Tabellen ausdrücklich auf Leerstand geprüft: Keys, Key-Findings, Revisionen, Reviews, Events, Operations, Publication runs.
- 42 Kontext-/Adapterprüfungen und sieben Mappingtests bestanden. Pint, PHP-Syntax- und Diff-Whitespace-Prüfung bestanden.

Ein privater Dump der neuen Testdatenbank wurde erstellt, SHA-256 geprüft und sein Inhaltsverzeichnis gelesen. Der Testcluster ist gestoppt. Eine eigene Restore-Probe dieses neuen Dumps wurde noch nicht durchgeführt. Pfad und Hash stehen im begleitenden JSON; Rohdaten liegen weiterhin außerhalb von Git.

## Grenzen und nächste Schritte

Der Importer ist ein Prototyp für die isolierte Probe, kein neuer produktiver Artisan-Command. Identische Wiederholung bedeutet hier denselben unveränderten Erfassungsmanifest-Hash. Ein **neuer Scannerlauf** mit erneut bestätigten, verschobenen oder entfernten Fundstellen ist ein anderer Vorgang und muss als nächster Schritt mit tatsächlichen Erfassungsdaten geprüft werden. Insbesondere dürfen dabei keine unveränderten Findings doppelt entstehen und keine obsolete Findings reaktiviert werden.

Die 72 offenen Kandidaten sind vollständig bilanziert, aber noch nicht fachlich aufgelöst. Schlüsselidentitäten, Zuordnungen und Übersetzungsrevisionen werden erst in getrennten, nachvollziehbaren Schritten aufgebaut. Gleiche Literale lösen keine automatische Key-Zusammenführung aus.
