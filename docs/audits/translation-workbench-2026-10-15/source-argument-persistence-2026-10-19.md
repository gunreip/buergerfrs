# Herkunftsnachweise gespeichert – 19.10.2026

Der zuletzt geprüfte Quellenrollen-Dump wurde in eine neue isolierte PostgreSQL-Datenbank restauriert. Dort wurden ausschließlich drei ergänzende Tabellen angelegt; die 19 zuvor vorhandenen Tabellen blieben inhaltlich unverändert. Kein produktiver Datenbankzugriff, kein Anwendungsbootstrap, keine aktive Laravel-Migration.

## Gespeicherte Nachweise

- `source_argument_batches`: Prüfsumme des Argumentberichts, Capture-Manifest und zugrunde liegender Review-Nachweis.
- `source_argument_producers`: sechs Wertlieferanten mit Quelldatei, Dateihash, Member und den beobachteten Ausdrücken.
- `source_argument_evidence`: 59 getrennte Fundstellennachweise, jeweils verknüpft mit Capture und Quellenrolle; sieben davon referenzieren einen der sechs Wertlieferanten.

Die 59 Nachweise bestehen aus 28 Optionsaufrufen, drei überschreibbaren Defaults, sieben Producer-Zuordnungen, elf weiterhin laufzeitabhängigen Quellen und zehn offenen Literal/Key-Entscheidungen. Expressions, NULL-Werte und leere Strings werden als JSON-Evidenz erhalten. Ein Options-Key wird nicht als skalare Translation importiert. Die 13 Serialisierungen erhalten keine zusätzlichen Übersetzungsnachweise.

Es gibt keine Verknüpfung dieser neuen Tabellen mit einer Freigabe oder Translation-Revision. Die ursprünglichen Pending-Zustände und alle Finding-Rohfelder bleiben unverändert. Zusätzliche Evidenz ist keine fachliche Entscheidung.

## Prüfungen

82 PostgreSQL-Assertions bestanden:

- Verbindung ausschließlich zum vorgesehenen lokalen Testcluster, ohne TCP-Listener.
- Vollständiger Import und identischer Retry ohne Verdoppelung.
- Rücklesevergleich aller 59 Fundstellennachweise und sechs Producer-Nachweise.
- Vollständiger Rollback bei doppelten oder fehlenden Captures, fremden Reviews, vorweggenommenen Entscheidungen, falscher Quellenrolle, fehlenden Producern und erfundenem Literal für ein dynamisches Optionsargument.
- UPDATE und DELETE für alle drei Evidenztabellen abgewiesen.
- Inhaltsfingerabdrücke sämtlicher 19 bestehender Tabellen unverändert; damit auch Findings, Keys, Zuordnungen, Werte, Reviews und Events.

Zusätzlich bestanden erneut 16 Argumenttests und 15 Rollentests. Codeformatierung und Syntaxprüfung bestanden. Der Ergebnisdump ist privat gespeichert und der Testcluster wurde beendet. Der Eingangs-Dump wurde tatsächlich restauriert; der neu erzeugte Ergebnisdump wurde noch nicht erneut restauriert.

## Grenzen und nächster fachlicher Schritt

Die Tabellen und der Importer gehören weiterhin ausschließlich zum Rehearsal unter `tests/Support`. Die Herkunftsprüfung arbeitet gegen die gesicherte Quelle, nicht gegen den inzwischen veränderlichen Arbeitsbaum. Normale Updates/Deletes werden blockiert; dies ist kein Schutz vor administrativen Schemaänderungen oder TRUNCATE. Statische Producer-Beobachtungen beweisen keine vollständige Laufzeit-Wertemenge.

Die Quellerfassung und ihre Evidenz sind damit für den geprüften Umfang persistiert. Als nächster fachlicher Schritt steht die getrennte Key-/Finding-Zuordnung an: vorhandene veröffentlichte Keys und Werte exakt belegen, ohne gleiche Literale automatisch zusammenzufassen, Findings zu vereinigen oder Freigaben aus Altdaten abzuleiten. Die 31 ungeklärten Übersetzungsquellen bleiben dabei ausdrücklich offen.

[Maschinenlesbares Prüfergebnis](source-argument-persistence-2026-10-19.json)
