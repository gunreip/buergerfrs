# Getrennte Quellenrollen – Probe vom 19.10.2026

Die statische Probe-Erfassung wurde in eine neue, isolierte PostgreSQL-Datenbank restauriert. Eine zusätzliche unveränderliche Tabelle ordnet jedem Capture eine Rolle zu. Bestehende Importnachweise, Findings und Rohfelder bleiben erhalten; keine produktive Verbindung und kein Anwendungsbootstrap.

| Rolle | Anzahl | Stand |
|---|---:|---|
| Übersetzungsquelle, statisch erfasst | 19.940 | Bestehendes Finding referenziert |
| Übersetzungsquelle, ungeklärt | 31 | Offen, kein neues Finding |
| Optionsquelle | 28 | Eigener Rollennachweis, keine skalare Übersetzung |
| Verwendung / Serialisierung | 13 | Eigener Rollennachweis, keine zusätzliche Übersetzungsaufgabe |
| Ausgeschlossener Kontext | 108 | Kommentar oder Stringinhalt |
| Gesamt | 20.120 | Jeder Capture genau einmal bilanziert |

## Bedeutung und Grenzen

Eine Quellenrolle ist keine Freigabe. Alle 72 zuvor offenen Kandidaten bleiben im ursprünglichen Importnachweis offen. Die neue Projektion ergänzt diesen Nachweis; sie schreibt ihn nicht um. Übersetzungsquellen mit dynamischen Argumenten behalten ihre Unsicherheit. Optionsquellen enthalten die bisher belegte Owner-/Sprachdatei-Evidenz, aber noch keine vollständig aufgelöste Laufzeit-Optionsidentität.

Die sechs Verwendungen übersetzter Props bewahren den Default-Beleg. Es wird ausdrücklich nicht behauptet, dass jeder Aufrufer diesen Default benutzt. Es wurden noch keine verbindlichen Verwendungsbeziehungen zu einem konkreten Default-Finding gespeichert. Das erfordert einen eindeutigen Beleg des Ursprungsaufrufs, nicht nur gleichen Text.

Dies ist ein Testmodell unter `tests/Support`, keine aktive Laravel-Migration und keine Änderung am produktiven Scanner. Die Klassifikation verarbeitet die vorhandenen entdeckten Captures; sie beweist nicht die vollständige Erkennung sämtlicher Quellkonstrukte.

## Prüfungen

- 15 Rollen-Assertions: Rollentrennung, unveränderte Eingangsdaten, fehlende oder fremde Reviews, unbekannte Kategorien, ungeklärter Kontext und unzulässige Vorentscheidungen.
- 11 PostgreSQL-Assertions: isolierte Verbindung, vollständige Abdeckung, unveränderte Findings, keine erzeugten Keys/Zuordnungen/Revisionen/Reviews/Events, keine skalaren Findings für Verwendung oder Optionen, Datenbank-Constraint und unveränderlicher Nachweis.
- Erneut acht Prüfungen der offenen Kandidaten und 42 Capture-/Kontextprüfungen bestanden.

Für den Constraint-Test wurde der Schreibschutz ausschließlich innerhalb einer zurückgerollten Testtransaktion deaktiviert. Anschließend wurde der aktive Schreibschutz separat geprüft. Keine Rollenänderung wurde dabei persistiert. Das Modell schützt normale Datenoperationen, nicht einen administrativen Superuser-Eingriff.

Der Ergebnisdump wurde privat gespeichert und der Testcluster beendet. Ein erneuter Restore dieses neuen Dumps wurde noch nicht durchgeführt. Prüfsummen und Ergebnis: [source-roles-2026-10-19.json](source-roles-2026-10-19.json).

## Nächster Schritt

Die 31 offenen Übersetzungsquellen anhand ihrer tatsächlichen Wertproduzenten weiter prüfen und die Optionsidentität/Fallbacks getrennt modellieren. Eindeutige Belege dürfen ergänzt werden; ungeklärte Fälle bleiben sichtbar offen. Keine automatische Key-Vereinheitlichung oder Ableitung eines Literals aus einem Key-Namen.
